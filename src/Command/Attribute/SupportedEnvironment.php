<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class SupportedEnvironment
{
    public function __construct(public string $value)
    {
    }
}
