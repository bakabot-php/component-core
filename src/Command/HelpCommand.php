<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Action\ActionInterface;

final class HelpCommand extends AbstractCommand
{
    private Collection $commands;
    private ?string $requestedCommand;

    public function __construct(Collection $commands, ?string $requestedCommand = null)
    {
        $this->commands = $commands;
        $this->requestedCommand = $requestedCommand;
    }

    private function trimPrefix(?string $commandName): ?string
    {
        if ($commandName === null) {
            return null;
        }

        $commandName = ltrim($commandName, $this->getCommandPrefix());

        return $commandName === '' ? null : $commandName;
    }

    private function createCommandHelp(CommandInterface $command): ActionInterface
    {

    }

    private function createCommandOverview(): ActionInterface
    {

    }

    public function getArgumentExpression(): string
    {
        return '[command:string]';
    }

    public function getDescription(?CommandInterface $command = null): string
    {
        if ($command && !$this->isFallback()) {
            return $this->getDescriptionOverview();
        }

        return $this->getDescriptionDefault();
    }

    public function getName(): string
    {
        return 'help';
    }

    public function isFallback(): bool
    {
        return $this->requestedCommand !== $this->getName();
    }

    public function run(): ActionInterface
    {
        $this->requestedCommand = $this->trimPrefix($this->getArgument('command'));

        if ($this->requestedCommand === $this->getName()) {
            return $this->createCommandHelp($this);
        }

        if (
            !$this->requestedCommand
            || ($commandToHelpWith = $this->commands->findByName($this->requestedCommand)) === null
        ) {
            return $this->createCommandOverview();
        }

        return $this->createCommandHelp($commandToHelpWith);
    }
}
