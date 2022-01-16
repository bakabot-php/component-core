<?php

declare(strict_types = 1);

namespace Bakabot\Command\Prefix;

use Stringable;

/** @psalm-immutable */
final class Prefix implements PrefixSource
{
    public /* readonly */ int $length;
    public /* readonly */ string $prefix;

    public function __construct(string|Stringable $prefix)
    {
        $trimmedPrefix = trim((string) $prefix);
        assert($trimmedPrefix !== '');

        $this->length = mb_strlen($trimmedPrefix);
        $this->prefix = $trimmedPrefix;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getPrefix(): Prefix
    {
        return $this;
    }

    public function matches(string $message): bool
    {
        // str_starts_with(), but we know the length, so we can actually save some overhead here
        return strncmp($message, $this->prefix, $this->length) === 0;
    }

    public function __toString(): string
    {
        return $this->prefix;
    }
}
