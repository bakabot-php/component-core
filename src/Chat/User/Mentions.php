<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use ArrayIterator;
use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Identifiable;
use Bakabot\Chat\Server\Server;
use Countable;
use IteratorAggregate;
use Traversable;

final class Mentions implements IteratorAggregate, Countable
{
    /** @var Identifiable[] */
    private array $mentions = [];

    /**
     * @param Identifiable[] $mentions
     */
    public function __construct(Identifiable ...$mentions)
    {
        foreach ($mentions as $item) {
            $this->mentions[$item->id()] = $item;
        }
    }

    /**
     * @param class-string<Identifiable> $type
     * @return Mentions
     */
    private function pickType(string $type): Mentions
    {
        return new Mentions(...array_filter($this->mentions, static fn ($item) => is_a($item, $type)));
    }

    public function channels(): Mentions
    {
        return $this->pickType(Channel::class);
    }

    public function count(): int
    {
        return count($this->mentions);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->mentions);
    }

    public function has(Identifiable $item): bool
    {
        return isset($this->mentions[$item->id()])
            && is_a($this->mentions[$item->id()], get_class($item));
    }

    public function roles(): Mentions
    {
        return $this->pickType(Role::class);
    }

    public function servers(): Mentions
    {
        return $this->pickType(Server::class);
    }

    public function users(): Mentions
    {
        return $this->pickType(User::class);
    }
}
