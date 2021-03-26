<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

use PHPUnit\Framework\TestCase;

abstract class AbstractAttributeTest extends TestCase
{
    protected const ATTRIBUTE = null;
    protected const VALUE = null;

    /** @test */
    public function contains_the_given_value(): void
    {
        $class = static::ATTRIBUTE;

        self::assertSame(static::VALUE, (new $class(static::VALUE))->value);
    }
}
