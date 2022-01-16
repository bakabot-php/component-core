<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message;

use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Target;
use Bakabot\Chat\User\Mentions;
use Bakabot\Chat\User\User;
use Bakabot\Environment;
use DateTimeInterface;

final class Message implements Target
{
    private /* readonly */ Mentions $mentions;

    public function __construct(
        public /* readonly */ string $id,
        public /* readonly */ Environment $environment,
        public /* readonly */ User $author,
        public /* readonly */ Channel $channel,
        public /* readonly */ string $content,
        public /* readonly */ DateTimeInterface $creationTime,
        public /* readonly */ ?DateTimeInterface $modifiedTime,
    ) {
    }

    public function environment(): Environment
    {
        return $this->environment;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function mentions(): Mentions
    {
        if (!isset($this->mentions)) {
            $this->mentions = $this->environment->parseMentions($this->content);
        }

        return $this->mentions;
    }

    public function wasModified(): bool
    {
        return $this->modifiedTime !== null;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
