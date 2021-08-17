<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Chat\Server\Settings\ServerSettings;
use Bakabot\Chat\Server\Settings\ServerSettingsSourceInterface;
use Bakabot\Command\Registry;
use Bakabot\Payload\PayloadInterface;
use Generator;

final class ProcessorFactory
{
    private Registry $registry;
    private ServerSettingsSourceInterface $serverSettingsSource;

    public function __construct(Registry $registry, ServerSettingsSourceInterface $serverSettingsSource)
    {
        $this->registry = $registry;
        $this->serverSettingsSource = $serverSettingsSource;
    }

    /**
     * @param PayloadInterface $payload
     * @return Promise<ServerSettings>
     */
    private function getServerSettings(PayloadInterface $payload): Promise
    {
        return $this->serverSettingsSource->getServerSettings($payload);
    }

    public function createCommandParser(): LazyProcessor
    {
        /** @var callable(PayloadInterface $payload): CommandParser $factory */
        $factory = function (PayloadInterface $payload): Generator {
            /** @var ServerSettings $settings */
            $settings = yield $this->getServerSettings($payload);

            return new CommandParser($settings->getPrefix());
        };

        return new LazyProcessor($factory);
    }

    public function createCommandRunner(): LazyProcessor
    {
        /** @var callable(PayloadInterface $payload): CommandRunner $factory */
        $factory = function (PayloadInterface $payload): Generator {
            /** @var ServerSettings $settings */
            $settings = yield $this->getServerSettings($payload);
            $registry = $this->registry->filterByEnvironment($payload->getEnvironment()->getName());

            $allowedCommands = $settings->getAllowedCommands();
            if ($allowedCommands->count() > 0) {
                $registry = $registry->filterByNames($allowedCommands);
            }

            return new CommandRunner($registry->getCommands());
        };

        return new LazyProcessor($factory);
    }
}
