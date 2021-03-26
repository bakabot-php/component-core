<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class SupportedEnvironments
{
    public function __construct(public array $value)
    {
    }
}
