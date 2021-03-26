<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Emoji;

interface FormatterInterface
{
    /**
     * @param string $template
     * @param array<string, mixed> $context
     * @return string
     */
    public function format(string $template, array $context = []): string;
}
