<?php

declare(strict_types = 1);

namespace Bakabot;

use Bakabot\Chat\Message\Message;
use Bakabot\Chat\User\User;

interface Driver
{
    public static function create(User $user): static;

    public function createMessage(mixed $data): Message;

    public function environment(): Environment;
}
