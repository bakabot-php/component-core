<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall\Rule;

use Amp\Promise;
use Bakabot\Chat\Channel\ServerChannel;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Server\Settings\Settings;
use Bakabot\Chat\Server\Settings\SettingsSource;
use Bakabot\Processor\Firewall\RuleViolation;
use function Amp\call;

final class ServerAllowList extends FirewallRule
{
    private SettingsSource $settingsSource;

    public function __construct(SettingsSource $settingsSource)
    {
        $this->settingsSource = $settingsSource;
    }

    public function detailedMessage(Message $message): ?string
    {
        $channel = $message->channel;

        if (!($channel instanceof ServerChannel)) {
            return null;
        }

        return sprintf(
            "Ignoring message in %s channel [%s] (server: [%s]) - not on server's allow list",
            ucfirst(strtolower($message->environment()->name())),
            $channel->id(),
            $channel->server()->id()
        );
    }

    /**
     * @param Message $message
     * @return Promise<?RuleViolation>
     */
    public function handle(Message $message): Promise
    {
        return call(function () use ($message) {
            // only applies to servers
            $channel = $message->channel;

            if (!($channel instanceof ServerChannel)) {
                return parent::handle($message);
            }

            /** @var Settings $settings */
            $settings = yield $this->settingsSource->settings($message);

            if (!$settings->allowList->contains($channel)) {
                return $this->fail();
            }

            return parent::handle($message);
        });
    }

    public function name(): string
    {
        return self::PREFIX_SERVER . 'allow-list';
    }
}
