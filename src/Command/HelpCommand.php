<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Amp\Promise;
use Amp\Success;
use Bakabot\Action\Action;
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
    private Commands $commands;

    /** @var string */
    public const NAME = 'help';
    /** @var string */
    public const TEMPLATE_COMMAND_HELP = 'help.command_help';
    /** @var string */
    public const TEMPLATE_HELP_OVERVIEW = 'help.command_overview';

    public function __construct(Commands $commands)
    {
        $this->commands = $commands;
    }

    private function commandHelp(Command $command): SendTemplatedMessage
    {
        return SendTemplatedMessage::to($this->payload->message->channel)
            ->withTemplate(
                self::TEMPLATE_COMMAND_HELP,
                [
                    'command' => $command,
                    'prefix' => $this->prefix(),
                ]
            )
        ;
    }

    private function requestedCommand(): ?string
    {
        /** @var string|null $commandName */
        $commandName = $this->argument('command');

        if ($commandName === null) {
            return null;
        }

        /** @var string $commandName */
        $commandName = ltrim($commandName, $this->prefix());

        return $commandName === '' ? null : strtolower($commandName);
    }

    protected function execute(): Action
    {
        $requestedCommand = $this->requestedCommand();

        switch ($requestedCommand) {
            case null:
                return self::commandListing($this->commands, $this->payload);

            case $this->name():
                return $this->commandHelp($this);
        }

        $command = $this->commands->findByName($requestedCommand);

        if ($command === null) {
            return self::commandListing($this->commands, $this->payload, $requestedCommand);
        }

        return $this->commandHelp($command);
    }

    /**
     * @param Commands $commands
     * @param Payload $payload
     * @param string|null $requestedCommand
     * @return Promise<SendTemplatedMessage>
     */
    public static function commandListing(
        Commands $commands,
        Payload $payload,
        ?string $requestedCommand = null
    ): Promise {
        return new Success(
            SendTemplatedMessage::to($payload->message->channel)
                ->withTemplate(
                    self::TEMPLATE_HELP_OVERVIEW,
                    [
                        'commands' => $commands,
                        'prefix' => $payload->trigger->prefix(),
                        'requested_command' => $requestedCommand,
                    ]
                )
        );
    }
}
