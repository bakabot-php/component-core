<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Emoji;

use PHPUnit\Framework\TestCase;

class ReplacementsTest extends TestCase
{
    /** @test */
    public function contains_nothing_by_default(): void
    {
        $replacements = new Replacements();

        self::assertSame([], $replacements->toArray());
    }

    /** @test */
    public function returns_set_replacements(): void
    {
        $replacementsArray = [
            'greeting' => 'Hello World',
        ];

        $replacements = new Replacements($replacementsArray);

        self::assertSame($replacementsArray, $replacements->toArray());
    }

    /** @test */
    public function returns_defaults_merged_with_custom_replacements(): void
    {
        $replacements = Replacements::withDefaults(
            [
                'greeting' => 'Hello World',
            ]
        );

        $replacementsArray = $replacements->toArray();
        end($replacementsArray);

        self::assertGreaterThan(1, count($replacements));
        self::assertSame('greeting', key($replacementsArray));
        self::assertSame('Hello World', current($replacementsArray));
    }

    /** @test */
    public function merge_returns_copy(): void
    {
        $defaults = Replacements::withDefaults();

        $replacementsArray = [
            'greeting' => 'Hello World',
        ];

        $replacements = new Replacements($replacementsArray);

        $merged = $defaults->merge($replacements);

        self::assertNotSame($defaults, $merged);
        self::assertNotSame($replacements, $merged);
        self::assertCount(count($defaults) + count($replacements), $merged);
    }
}
