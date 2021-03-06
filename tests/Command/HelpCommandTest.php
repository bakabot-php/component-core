<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Amp\Promise;
use Bakabot\Action\SendTemplatedMessage;
use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class HelpCommandTest extends TestCase
{
    private function createTestPayload(string $requestedCommand = ''): Payload
    {
        return new Payload(
            $this->createMock(PayloadInterface::class),
            '!',
            HelpCommand::NAME,
            $requestedCommand !== '' ? [$requestedCommand] : [],
            $requestedCommand
        );
    }

    /** @test */
    public function can_produce_help_overview_without_instantiation(): void
    {
        $action = Promise\wait(HelpCommand::createCommandOverview(new Collection(), $this->createTestPayload()));

        self::assertInstanceOf(ChannelInterface::class, $action->getTarget());
        self::assertSame(HelpCommand::TEMPLATE_HELP_OVERVIEW, $action->getMessage());
    }

    /** @test */
    public function produces_help_overview_when_invoked_directly(): void
    {
        $command = new HelpCommand(new Collection());
        $command->bind($this->createTestPayload());

        $action = Promise\wait($command->run());

        self::assertInstanceOf(SendTemplatedMessage::class, $action);
        self::assertInstanceOf(ChannelInterface::class, $action->getTarget());
        self::assertSame(HelpCommand::TEMPLATE_HELP_OVERVIEW, $action->getMessage());
        self::assertNull($action->getContext()['requested_command']);
    }

    /** @test */
    public function produces_command_help_for_itself(): void
    {
        $command = new HelpCommand(new Collection());
        $command->bind($this->createTestPayload('help'));

        $action = Promise\wait($command->run());

        self::assertInstanceOf(SendTemplatedMessage::class, $action);
        self::assertInstanceOf(ChannelInterface::class, $action->getTarget());
        self::assertSame(HelpCommand::TEMPLATE_COMMAND_HELP, $action->getMessage());
        self::assertSame($command, $action->getContext()['command']);
    }

    /** @test */
    public function falls_back_to_overview_if_unknown_command_requested(): void
    {
        $name = 'quote';

        $command = new HelpCommand(new Collection());
        $command->bind($this->createTestPayload($name));

        $action = Promise\wait($command->run());

        self::assertInstanceOf(SendTemplatedMessage::class, $action);
        self::assertInstanceOf(ChannelInterface::class, $action->getTarget());
        self::assertSame(HelpCommand::TEMPLATE_HELP_OVERVIEW, $action->getMessage());
        self::assertSame($name, $action->getContext()['requested_command']);
    }

    /** @test */
    public function produces_command_help_for_known_command(): void
    {
        $name = 'fake';

        $fakeCommand = $this->createMock(CommandInterface::class);
        $fakeCommand
            ->method('getName')
            ->willReturn($name);

        $commands = new Collection();
        $commands->push($name, $fakeCommand);

        $helpCommand = new HelpCommand($commands);
        $helpCommand->bind($this->createTestPayload($name));

        $action = Promise\wait($helpCommand->run());

        self::assertInstanceOf(SendTemplatedMessage::class, $action);
        self::assertInstanceOf(ChannelInterface::class, $action->getTarget());
        self::assertSame(HelpCommand::TEMPLATE_COMMAND_HELP, $action->getMessage());
        self::assertSame($fakeCommand, $action->getContext()['command']);
    }
}
