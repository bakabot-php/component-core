<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Success;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Settings\AllowedCommands;
use Bakabot\Chat\Server\Settings\ChannelList;
use Bakabot\Chat\Server\Settings\ServerSettings;
use Bakabot\Chat\Server\Settings\ServerSettingsSourceInterface;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Registry;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class ProcessorFactoryTest extends TestCase
{
    /** @test */
    public function can_create_command_parser(): void
    {
        $settingsSource = $this->createMock(ServerSettingsSourceInterface::class);
        $settingsSource->expects($this->once())->method('getServerSettings')->willReturn(
            new Success(ServerSettings::withDefaults(new Language('en'), new Prefix('!')))
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
        $serverSettingsSource = $this->createMock(ServerSettingsSourceInterface::class);
        $serverSettingsSource->expects($this->once())->method('getServerSettings')->willReturn(
            new Success(
                new ServerSettings(
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
