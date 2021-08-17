<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall\Rule;

use Amp\Failure;
use Amp\Promise;
use Amp\Success;
use Bakabot\Chat\Server\Settings\ServerSettings;
use Bakabot\Chat\Server\Settings\ServerSettingsSourceInterface;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\RuleViolation;

use function Amp\call;

final class ServerAllowList implements RuleInterface
{
    private ServerSettingsSourceInterface $settingsSource;

    public function __construct(ServerSettingsSourceInterface $settingsSource)
    {
        $this->settingsSource = $settingsSource;
    }

    public function getDetailedMessage(PayloadInterface $payload): string
    {
        $server = $payload->getServer();
        assert($server !== null);

        $channel = $payload->getChannel();

        return sprintf(
            "Ignoring message in %s channel [%s] (server: [%s]) - not on server's allow list",
            ucfirst(strtolower($payload->getEnvironment()->getName())),
            $channel->getName() ?? $channel->getId(),
            $server->getName() ?? $server->getId()
        );
    }

    /**
     * @param PayloadInterface $payload
     * @return Promise<PayloadInterface|RuleViolation>
     */
    public function enforce(PayloadInterface $payload): Promise
    {
        return call(function () use ($payload) {
            $server = $payload->getServer();

            // only applies to servers
            if ($server === null) {
                /** @var Success<PayloadInterface> $success */
                $success = new Success($payload);
                return $success;
            }

            $settings = yield $this->settingsSource->getServerSettings($payload);

            $allowList = $settings->getAllowList();
            $channelId = $payload->getChannel()->getId();

            if ($allowList->count() > 0 && $allowList->contains($channelId) === false) {
                /** @var Failure<RuleViolation> $failure */
                $failure = new Failure(new RuleViolation($this));
                return $failure;
            }

            /** @var Success<PayloadInterface> $success */
            $success = new Success($payload);
            return $success;
        });
    }

    public function getName(): string
    {
        return self::PREFIX_SERVER . 'allow-list';
    }

}
