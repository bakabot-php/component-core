<?php

declare(strict_types = 1);

namespace Bakabot\Command;

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
        $commandName = $this->getArgument('command');

        if ($commandName === null) {
            return null;
        }

        $commandName = ltrim($commandName, $this->getCommandPrefix());

        return $commandName === '' ? null : strtolower($commandName);
    }

    private function createCommandHelp(CommandInterface $command): ActionInterface
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

    public static function createCommandOverview(
        Collection $commands,
        Payload $payload,
        ?string $requestedCommand = null
    ): ActionInterface {
        return new SendTemplatedMessage(
            $payload->getChannel(),
            self::TEMPLATE_HELP_OVERVIEW,
            [
                'commands' => $commands,
                'prefix' => $payload->getCommandPrefix(),
                'requested_command' => $requestedCommand,
            ]
        );
    }

    public function run(): ActionInterface
    {
        $requestedCommand = $this->getNormalizedCommandName();

        if ($requestedCommand === null) {
            return self::createCommandOverview($this->commands, $this->getPayload());
        }

        if ($requestedCommand === $this->getName()) {
            return $this->createCommandHelp($this);
        }

        $command = $this->commands->findByName($requestedCommand);

        if ($command === null) {
            return self::createCommandOverview($this->commands, $this->getPayload(), $requestedCommand);
        }

        return $this->createCommandHelp($command);
    }
}
