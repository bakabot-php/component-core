<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use Throwable;

final class FallbackLanguageSource implements LanguageSourceInterface
{
    private LanguageSourceInterface $fallback;
    private LanguageSourceInterface $main;

    public function __construct(LanguageSourceInterface $main, LanguageSourceInterface $fallback)
    {
        $this->fallback = $fallback;
        $this->main = $main;
    }

    public function getLanguage(): Language
    {
        try {
            return $this->main->getLanguage();
        } catch (Throwable) {
            return $this->fallback->getLanguage();
        }
    }
}
