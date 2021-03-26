<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Generator;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

final class Collection implements IteratorAggregate
{
    /** @var array<string, CommandInterface> */
    private array $commands = [];

    public function findByName(string $commandName): ?CommandInterface
    {
        foreach ($this->commands as $name => $command) {
            if ($commandName === $name) {
                return $command;
            }
        }

        return null;
    }

    /**
     * @return Generator<string, CommandInterface>
     */
    public function getIterator(): Traversable
    {
        ksort($this->commands, SORT_NATURAL);
        reset($this->commands);

        foreach ($this->commands as $name => $command) {
            yield $name => $command;
        }
    }

    public function push(string $name, CommandInterface $command): void
    {
        if (isset($this->commands[$name])) {
            throw new InvalidArgumentException(sprintf('A command with the name [%s] already exists.', $name));
        }

        $this->commands[$name] = $command;
    }
}
