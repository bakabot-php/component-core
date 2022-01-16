<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Chat\Server\Settings\AllowedCommands;
use IteratorAggregate;
use IteratorIterator;
use Traversable;

final class Registry implements IteratorAggregate
{
    private Commands $commands;

    private const DELIMITER = ':';

    public function __construct()
    {
        $this->commands = new Commands();
    }

    public function add(Command $command): void
    {
        foreach ($command->supportedEnvironments() as $environment) {
            $this->commands->push(
                sprintf('%s%s%s', $environment, self::DELIMITER, $command->name()),
                $command
            );
        }
    }

    public function filterByEnvironment(string $environment): self
    {
        $copy = new self();

        foreach ($this->commands as $key => $command) {
            [$supportedEnvironment, $name] = explode(self::DELIMITER, $key);

            if ($supportedEnvironment === $environment) {
                $copy->commands->push($name, $command);
            }
        }

        return $copy;
    }

    /**
     * @param AllowedCommands|string[] $names
     * @return $this
     */
    public function filterByNames(AllowedCommands|array $names): self
    {
        if ($names instanceof AllowedCommands) {
            $names = $names->toArray();
        }

        $copy = new self();

        foreach ($this->commands as $name => $command) {
            if (in_array($name, $names, true)) {
                $copy->commands->push($name, $command);
            }
        }

        return $copy;
    }

    public function getCommands(): Commands
    {
        return $this->commands;
    }

    public function getIterator(): Traversable
    {
        return new IteratorIterator($this->getCommands());
    }

    public function merge(Registry $registry): self
    {
        $copy = clone $this;

        foreach ($registry->commands as $name => $command) {
            $copy->commands->push($name, $command);
        }

        return $copy;
    }

    public function __clone()
    {
        $this->commands = clone $this->commands;
    }
}
