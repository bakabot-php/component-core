<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Countable;
use Stringable;

final class ChannelList implements Countable
{
    /** @var array<string, array-key> */
    private array $keyed;

    /** @param array<array-key, string> $list */
    public function __construct(array $list = [])
    {
        $this->keyed = array_flip($list);
    }

    public function contains(string|Stringable $channelId): bool
    {
        return isset($this->keyed[(string) $channelId]);
    }

    public function count(): int
    {
        return count($this->keyed);
    }
}
