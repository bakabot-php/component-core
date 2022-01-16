<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Success;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Settings\AllowedCommands;
use Bakabot\Chat\Server\Settings\ChannelList;
use Bakabot\Chat\Server\Settings\Settings;
use Bakabot\Chat\Server\Settings\SettingsSource;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Registry;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class ProcessorFactoryTest extends TestCase
{
    /** @test */
    public function can_create_command_parser(): void
    {
        $settingsSource = $this->createMock(SettingsSource::class);
        $settingsSource->expects($this->once())->method('getServerSettings')->willReturn(
            new Success(Settings::withDefaults(new Language('en'), new Prefix('!')))
        );

        $factory = new ProcessorFactory(new Registry(), $settingsSource);
        $processor = $factory->createCommandParser();
        assert($processor instanceof LazyProcessor);

        $commandParser = $processor->getInnerProcessor($this->createMock(PayloadInterface::class));

        self::assertInstanceOf(CommandParser::class, $commandParser);
    }

    /** @test */
    public function can_create_command_runner(): void
    {
        $serverSettingsSource = $this->createMock(SettingsSource::class);
        $serverSettingsSource->expects($this->once())->method('getServerSettings')->willReturn(
            new Success(
                new Settings(
                    new Language('en'),
                    new Prefix('!'),
                    new AllowedCommands(['test']),
                    new ChannelList(),
                    new ChannelList()
                )
            )
        );

        $factory = new ProcessorFactory(new Registry(), $serverSettingsSource);
        $processor = $factory->createCommandRunner()->getInnerProcessor($this->createMock(PayloadInterface::class));

        self::assertInstanceOf(CommandRunner::class, $processor);
    }
}
