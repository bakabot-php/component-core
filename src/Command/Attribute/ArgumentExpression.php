<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class ArgumentExpression
{
    public function __construct(public string $value)
    {
    }
}
