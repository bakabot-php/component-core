<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Action\ActionInterface;

abstract class AbstractHelpCommand extends AbstractCommand
{
    private Collection $commands;
    private ?string $requestedCommand;

    public function __construct(Collection $commands, ?string $requestedCommand = null)
    {
        $this->commands = $commands;
        $this->requestedCommand = $requestedCommand;
    }

    private function getFlavorText(): string
    {
        if ($this->isFallback()) {
            assert(is_string($this->requestedCommand));

            return $this->getCommandFallbackText($this->getCommandPrefix(), $this->requestedCommand);
        }

        return $this->getGreetingText();
    }

    private function trimPrefix(?string $commandName): ?string
    {
        if ($commandName === null) {
            return null;
        }

        return ltrim($commandName, $this->getCommandPrefix());
    }

    abstract protected function createCommandHelp(CommandInterface $command): ActionInterface;

    abstract protected function createCommandOverview(): ActionInterface;

    protected function getCommandFallbackText(string $prefix, string $requestedCommand): string
    {
        return sprintf(
            "Sorry, but I don't have a %s%s command. :(",
            $prefix,
            $requestedCommand
        );
    }

    protected function getDescriptionDefault(): string
    {
        return 'Helps help-seekers help themselves. Helpful!';
    }

    protected function getDescriptionOverview(): string
    {
        $p = $this->getCommandPrefix();
        $helpCommand = $this->getName();

        return sprintf(
            "Here's a list of available commands. Type \"%s%s <command>\" to find out more about a command!",
        );
    }

    protected function getGreetingText(): string
    {
        return 'Happy to help, pyon!';
    }

    final public function getArgumentExpression(): string
    {
        return '[command:string]';
    }

    final public function getDescription(?CommandInterface $command = null): string
    {
        if ($command && !$this->isFallback()) {
            return $this->getDescriptionOverview();
        }

        return $this->getDescriptionDefault();
    }

    final public function getName(): string
    {
        return HelpCommandInterface::NAME;
    }

    final public function isFallback(): bool
    {
        return $this->requestedCommand !== $this->getName();
    }

    final public function run(): ActionInterface
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
