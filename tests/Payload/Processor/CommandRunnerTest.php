<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Promise;
use Amp\Success;
use Bakabot\Action\ActionInterface;
use Bakabot\Action\DoNothing;
use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Command\AbstractCommand;
use Bakabot\Command\Collection as CommandCollection;
use Bakabot\Command\HelpCommand;
use Bakabot\Command\Payload as CommandPayload;
use Bakabot\EnvironmentInterface;
use Bakabot\Payload\Payload;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class CommandRunnerTest extends TestCase
{
    private function createCommandPayload(?HelpCommand $helpCommand = null): CommandPayload
    {
        $helpCommand ??= $this->createMock(HelpCommand::class);

        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getHelpCommand')->willReturn($helpCommand);

        return new CommandPayload(
            new Payload(
                $environment,
                $this->createMock(ChannelInterface::class),
                $this->createMock(MessageInterface::class),
                null,
            ),
            '!',
            'test',
            [],
            ''
        );
    }

    /** @test */
    public function returns_original_payload_if_its_not_a_command_payload(): void
    {
        $payload = $this->createMock(PayloadInterface::class);

        $processor = new CommandRunner(new CommandCollection());
        $returnedPayload = Promise\wait($processor->process($payload));

        self::assertSame($payload, $returnedPayload);
    }

    /** @test */
    public function resolves_with_help_command_action_if_command_unknown(): void
    {
        $payload = $this->createCommandPayload();

        $processor = new CommandRunner(new CommandCollection());
        $action = Promise\wait($processor->process($payload));

        self::assertInstanceOf(ActionInterface::class, $action);
    }

    /** @test */
    public function resolves_with_command_return_action(): void
    {
        $payload = $this->createCommandPayload();

        $command = $this->createMock(AbstractCommand::class);
        $command->expects($this->once())->method('bind')->with($payload);
        $command->method('getName')->willReturn('test');
        $command->method('run')->willReturn(new Success(new DoNothing()));

        $commands = new CommandCollection();
        $commands->push($command->getName(), $command);

        $processor = new CommandRunner($commands);
        $action = Promise\wait($processor->process($payload));

        self::assertInstanceOf(DoNothing::class, $action);
    }
}
