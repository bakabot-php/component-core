<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use Stringable;

final class Language implements LanguageSource
{
    public /* readonly */ string $language;

    public function __construct(string|Stringable $language)
    {
        $trimmedLanguage = trim((string) $language);
        assert($trimmedLanguage !== '');

        $this->language = $trimmedLanguage;
    }

    public function language(): Language
    {
        return $this;
    }

    public function __toString(): string
    {
        return $this->language;
    }
}
