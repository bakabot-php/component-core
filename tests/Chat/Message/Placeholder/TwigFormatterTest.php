<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Placeholder;

use PHPUnit\Framework\TestCase;
use Spatie\Emoji\Emoji;
use Twig\Environment as Twig;
use Twig\Loader\ArrayLoader;

class TwigFormatterTest extends TestCase
{
    private function createTwig(): Twig
    {
        return new Twig(new ArrayLoader());
    }

    /** @test */
    public function only_trims_templates_without_placeholders(): void
    {
        $input = ' Hello World ';
        $output = 'Hello World';

        $formatter = new TwigFormatter(new Replacements(), $this->createTwig());

        self::assertSame($output, $formatter->format($input));
    }

    /** @test */
    public function replaces_placeholders_with_known_replacements(): void
    {
        $input = "Can't wait to build all kinds of stupid bots with this. {{smile}}";
        $output = "Can't wait to build all kinds of stupid bots with this. " . Emoji::CHARACTER_SMILING_FACE;

        $formatter = new TwigFormatter(Replacements::withDefaults(), $this->createTwig());

        self::assertSame($output, $formatter->format($input));
    }

    /** @test */
    public function strips_unknown_placeholders(): void
    {
        $input = "{{ Well }} so much of this {{ that }} will just go poof. {{ boom }}";
        $output = "so much of this  will just go poof.";

        $formatter = new TwigFormatter(new Replacements(), $this->createTwig());

        self::assertSame($output, $formatter->format($input));
    }
}
