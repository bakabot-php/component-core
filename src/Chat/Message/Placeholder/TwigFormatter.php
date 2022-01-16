<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Placeholder;

use Stringable;
use Twig\Environment as Twig;

final class TwigFormatter implements Formatter
{
    private Replacements $replacements;
    private Twig $twig;

    public const TOKEN_CODE_BLOCK_START = '{%';
    public const TOKEN_VARIABLE_START = '{{';

    public function __construct(Replacements $replacements, Twig $twig)
    {
        $this->replacements = $replacements;
        $this->twig = $twig;
    }

    /**
     * @param string|Stringable $template
     * @param array<string, mixed> $context
     * @return string
     */
    public function format(string|Stringable $template, array $context = []): string
    {
        $template = trim((string) $template);

        if (
            str_contains($template, self::TOKEN_VARIABLE_START) === false &&
            str_contains($template, self::TOKEN_CODE_BLOCK_START) === false
        ) {
            return $template;
        }

        return trim(
            $this->twig
                ->createTemplate($template)
                ->render(array_replace($this->replacements->toArray(), $context))
        );
    }
}
