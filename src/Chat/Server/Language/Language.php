<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use Stringable;

final class Language implements LanguageSourceInterface, Stringable
{
    private string $language;

    public function __construct(string|Stringable $language)
    {
        $trimmedLanguage = trim((string) $language);
        assert($trimmedLanguage !== '');

        $this->language = $trimmedLanguage;
    }

    public function getLanguage(): Language
    {
        return $this;
    }

    public function __toString(): string
    {
        return $this->language;
    }
}
