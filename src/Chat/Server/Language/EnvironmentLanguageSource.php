<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use Locale;

final class EnvironmentLanguageSource implements LanguageSourceInterface
{
    public function getLanguage(): Language
    {
        /** @psalm-suppress UndefinedClass */
        $language = Locale::getPrimaryLanguage(Locale::getDefault());

        return new Language($language);
    }
}
