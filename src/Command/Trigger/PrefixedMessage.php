<?php

declare(strict_types = 1);

namespace Bakabot\Command\Trigger;

use Bakabot\Command\Prefix\PrefixSource;

final class PrefixedMessage implements Trigger
{
    public function __construct(
        public /* readonly */ PrefixSource $prefix
    ) {

    }

    public function prefix(): string
    {
        return (string) $this->prefix;
    }
}
