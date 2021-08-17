<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Amp\Deferred;
use Amp\Promise;
use Amp\Success;
use Bakabot\Action\ActionInterface;
use Bakabot\Action\SendTemplatedMessage;
use Bakabot\Command\Attribute\ArgumentExpression;
use Bakabot\Command\Attribute\Description;
use Bakabot\Command\Attribute\HelpText;
use Bakabot\Command\Attribute\Name;

#[ArgumentExpression('[command:string]')]
#[Description('{{ help.description }}')]
#[HelpText('{{ help.flavor_text }}')]
#[Name(HelpCommand::NAME)]
final class HelpCommand extends AbstractCommand
{
    private Collection $commands;

    /** @var string */
    public const NAME = 'help';
    /** @var string */
    public const TEMPLATE_COMMAND_HELP = 'help.command_help';
    /** @var string */
    public const TEMPLATE_HELP_OVERVIEW = 'help.command_overview';

    public function __construct(Collection $commands)
    {
        $this->commands = $commands;
    }

    private function getNormalizedCommandName(): ?string
    {
        /** @var string|null $commandName */
        $commandName = $this->getArgument('command');

        if ($commandName === null) {
            return null;
        }

        /** @var string $commandName */
        $commandName = ltrim($commandName, $this->getCommandPrefix());

        return $commandName === '' ? null : strtolower($commandName);
    }

    private function createCommandHelp(CommandInterface $command): SendTemplatedMessage
    {
        return new SendTemplatedMessage(
            $this->getPayload()->getChannel(),
            self::TEMPLATE_COMMAND_HELP,
            [
                'command' => $command,
                'prefix' => $this->getPayload()->getCommandPrefix(),
            ]
        );
    }

    /**
     * @param Collection $commands
     * @param Payload $payload
     * @param string|null $requestedCommand
     * @return Promise<SendTemplatedMessage>
     */
    public static function createCommandOverview(
        Collection $commands,
        Payload $payload,
        ?string $requestedCommand = null
    ): Promise {
        return new Success(
            new SendTemplatedMessage(
                $payload->getChannel(),
                self::TEMPLATE_HELP_OVERVIEW,
                [
                    'commands' => $commands,
                    'prefix' => $payload->getCommandPrefix(),
                    'requested_command' => $requestedCommand,
                ]
            )
        );
    }

    /** @return Promise<ActionInterface> */
    public function run(): Promise
    {
        $requestedCommand = $this->getNormalizedCommandName();

        if ($requestedCommand === null) {
            /** @var Promise<SendTemplatedMessage> $promise */
            $promise = $this->promise(self::createCommandOverview($this->commands, $this->getPayload()));
            return $promise;
        }

        if ($requestedCommand === $this->getName()) {
            return $this->action($this->createCommandHelp($this));
        }

        $command = $this->commands->findByName($requestedCommand);

        if ($command === null) {
            /** @var Promise<SendTemplatedMessage> $promise */
            $promise = $this->promise(self::createCommandOverview($this->commands, $this->getPayload(), $requestedCommand));
            return $promise;
        }

        return $this->action($this->createCommandHelp($command));
    }
}
