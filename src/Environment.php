<?php

declare(strict_types = 1);

namespace Bakabot;

use Bakabot\Action\Reply;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\User\Mentions;
use Bakabot\Chat\User\User;

interface Environment
{
    public function formatMentions(Mentions $mentions): string;

    public function name(): string;

    public function parseMentions(string $text): Mentions;

    public function replier(Message $message): Reply;

    public function user(): User;
}
