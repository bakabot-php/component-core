<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Countable;

final class AllowedCommands implements Countable
{
    /** @var string[] */
    private array $list;

    /** @param string[] $list */
    public function __construct(array $list = [])
    {
        $this->list = array_unique($list);
    }

    public function count(): int
    {
        return count($this->list);
    }

    /** @return string[] */
    public function toArray(): array
    {
        return $this->list;
    }
}
