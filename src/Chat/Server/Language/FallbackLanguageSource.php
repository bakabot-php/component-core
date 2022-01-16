<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use Throwable;

final class FallbackLanguageSource implements LanguageSource
{
    private LanguageSource $fallback;
    private LanguageSource $main;

    public function __construct(LanguageSource $main, LanguageSource $fallback)
    {
        $this->fallback = $fallback;
        $this->main = $main;
    }

    public function language(): Language
    {
        try {
            return $this->main->language();
        } catch (Throwable) {
            return $this->fallback->language();
        }
    }

    public function __toString()
    {
        return (string) $this->language();
    }
}
