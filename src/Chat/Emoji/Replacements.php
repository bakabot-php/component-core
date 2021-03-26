<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Emoji;

use Countable;
use Spatie\Emoji\Emoji;

final class Replacements implements Countable
{
    /** @var array<string, string> */
    private array $replacements;

    public const EMOJI_CRYING = 'cry';
    public const EMOJI_FROWNING = 'frown';
    public const EMOJI_HUGGING = 'hug';
    public const EMOJI_LAUGHING = 'laugh';
    public const EMOJI_PLEADING = 'plead';
    public const EMOJI_SMILING = 'smile';
    public const EMOJI_THINKING = 'think';

    /**
     * @param array<string, string> $replacements
     */
    public function __construct(array $replacements = [])
    {
        $this->replacements = array_flip(array_flip($replacements));
    }

    /**
     * @param array<string, string> $replacements
     * @return self
     */
    public static function withDefaults(array $replacements = []): self
    {
        return new self(
            array_replace(
                [
                    self::EMOJI_CRYING => Emoji::cryingFace(),
                    self::EMOJI_FROWNING => Emoji::frowningFace(),
                    self::EMOJI_HUGGING => Emoji::huggingFace(),
                    self::EMOJI_LAUGHING => Emoji::rollingOnTheFloorLaughing(),
                    self::EMOJI_PLEADING => Emoji::pleadingFace(),
                    self::EMOJI_SMILING => Emoji::smilingFace(),
                    self::EMOJI_THINKING => Emoji::thinkingFace(),
                ],
                $replacements
            )
        );
    }

    public function count(): int
    {
        return count($this->replacements);
    }

    public function merge(Replacements $placeholders): self
    {
        $copy = clone $this;
        $copy->replacements = array_replace($this->replacements, $placeholders->replacements);

        return $copy;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->replacements;
    }
}
