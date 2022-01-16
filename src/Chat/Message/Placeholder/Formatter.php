<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Placeholder;

use Stringable;

interface Formatter
{
    /**
     * @param string|Stringable $template
     * @param array<string, mixed> $context
     * @return string
     */
    public function format(string|Stringable $template, array $context = []): string;
}
