<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Bakabot\Chat\Channel\Channel;
use Countable;

final class ChannelList implements Countable
{
    /** @var array<string, array-key> */
    private array $keyed;

    /** @param array<array-key, string> $list */
    public function __construct(array $list = [])
    {
        $this->keyed = array_flip($list);
    }

    public function contains(string|Channel $channel): bool
    {
        return isset($this->keyed[(string) $channel]);
    }

    public function count(): int
    {
        return count($this->keyed);
    }
}
