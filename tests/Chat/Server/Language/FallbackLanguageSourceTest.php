<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class FallbackLanguageSourceTest extends TestCase
{
    /** @test */
    public function uses_fallback_on_any_error(): void
    {
        $main = $this->createMock(LanguageSource::class);
        $main->expects($this->once())->method('getLanguage')->willThrowException(new RuntimeException());

        $fallback = new FallbackLanguageSource($main, new Language('es'));
        self::assertSame('es', (string) $fallback->language());
    }
}
