<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Emoji;

use Twig\Environment as Twig;
use Twig\Loader\ArrayLoader;

final class TwigFormatter implements FormatterInterface
{
    private Replacements $replacements;
    private Twig $twig;

    public function __construct(Replacements $replacements, ?Twig $twig = null)
    {
        $this->replacements = $replacements;
        $this->twig = $twig ?? $this->createTwig();
    }

    private function createTwig(): Twig
    {
        return new Twig(new ArrayLoader());
    }

    /**
     * @param string $template
     * @param array<string, mixed> $context
     * @return string
     */
    public function format(string $template, array $context = []): string
    {
        $template = trim($template);

        if (str_contains($template, '{{') === false && str_contains($template, '{%') === false) {
            return $template;
        }

        return trim(
            $this->twig
                ->createTemplate($template)
                ->render(array_replace($this->replacements->toArray(), $context))
        );
    }
}
