<?php

declare(strict_types = 1);

namespace Bakabot\Component\Core\Common;

use ReflectionClass;

trait Cloneable
{
    protected function copy(mixed ...$replacements): static
    {
        /** @var static $clone */
        $clone = (new ReflectionClass(static::class))->newInstanceWithoutConstructor();

        foreach (get_object_vars($this) as $var => $value) {
            if (array_key_exists($var, $replacements)) {
                $value = $replacements[$var];
            }

            $clone->$var = $value;
        }

        return $clone;
    }
}
