<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use Locale;

final class EnvironmentLanguageSource implements LanguageSource
{
    public function language(): Language
    {
        /** @psalm-suppress UndefinedClass */
        $language = Locale::getPrimaryLanguage(Locale::getDefault());

        return new Language($language);
    }

    public function __toString(): string
    {
        return (string) $this->language();
    }
}
