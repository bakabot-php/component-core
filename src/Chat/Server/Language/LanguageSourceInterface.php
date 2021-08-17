<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

interface LanguageSourceInterface
{
    public function getLanguage(): Language;
}
