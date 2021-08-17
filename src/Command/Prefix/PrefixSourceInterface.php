<?php

declare(strict_types = 1);

namespace Bakabot\Command\Prefix;

interface PrefixSourceInterface
{
    public function getPrefix(): Prefix;
}
