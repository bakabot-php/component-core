<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Generator;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

final class Commands implements IteratorAggregate
{
    /** @var array<string, Command> */
    private array $commands = [];

    public function findByName(string $commandName): ?Command
    {
        return $this->commands[$commandName] ?? null;
    }

    /**
     * @return Generator<string, Command>
     */
    public function getIterator(): Traversable
    {
        ksort($this->commands, SORT_NATURAL);
        reset($this->commands);

        foreach ($this->commands as $name => $command) {
            yield $name => $command;
        }
    }

    public function push(string $name, Command $command): void
    {
        if (isset($this->commands[$name])) {
            throw new InvalidArgumentException(sprintf('A command with the name [%s] already exists.', $name));
        }

        $this->commands[$name] = $command;
    }
}
