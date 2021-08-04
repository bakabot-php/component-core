<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use IteratorAggregate;
use IteratorIterator;
use Traversable;

final class Registry implements IteratorAggregate
{
    private Collection $commands;

    private const DELIMITER = ':';

    public function __construct()
    {
        $this->commands = new Collection();
    }

    public function addCommand(CommandInterface $command): void
    {
        $name = $command->getName();

        foreach ($command->getSupportedEnvironments() as $environment) {
            $this->commands->push(
                sprintf('%s%s%s', $environment, self::DELIMITER, $name),
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
     * @param string[] $names
     * @return $this
     */
    public function filterByNames(array $names): self
    {
        $copy = new self();

        foreach ($this->commands as $name => $command) {
            if (in_array($name, $names, true)) {
                $copy->commands->push($name, $command);
            }
        }

        return $copy;
    }

    public function getCommands(): Collection
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
