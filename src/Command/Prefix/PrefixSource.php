<?php

declare(strict_types = 1);

namespace Bakabot\Command\Prefix;

use Stringable;

interface PrefixSource extends Stringable
{
    public function getPrefix(): Prefix;
}
