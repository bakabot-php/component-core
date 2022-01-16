<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Processor;

use Amp\Promise;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Server\Settings\Settings;
use Bakabot\Chat\Server\Settings\SettingsSource;
use Bakabot\Command\Registry;
use Generator;

final class ProcessorFactory
{
    private Registry $registry;
    private SettingsSource $serverSettingsSource;

    public function __construct(Registry $registry, SettingsSource $serverSettingsSource)
    {
        $this->registry = $registry;
        $this->serverSettingsSource = $serverSettingsSource;
    }

    /**
     * @param Message $message
     * @return Promise<Settings>
     */
    private function settings(Message $message): Promise
    {
        return $this->serverSettingsSource->settings($message);
    }

    public function createCommandParser(): LazyProcessor
    {
        /** @var callable(Message $message): Generator<CommandParser> */
        $factory = function (Message $message): Generator {
            /** @var Settings $settings */
            $settings = yield $this->settings($message);

            return new CommandParser($settings->prefix);
        };

        return new LazyProcessor($factory);
    }

    public function createCommandRunner(): LazyProcessor
    {
        /** @var callable(Message $message): Generator<CommandRunner> */
        $factory = function (Message $message): Generator {
            /** @var Settings $settings */
            $settings = yield $this->settings($message);
            $registry = $this->registry->filterByEnvironment($message->environment->name());

            $allowedCommands = $settings->allowedCommands;
            if ($allowedCommands->count() > 0) {
                $registry = $registry->filterByNames($allowedCommands);
            }

            return new CommandRunner($registry->getCommands());
        };

        return new LazyProcessor($factory);
    }
}
