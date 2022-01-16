<?php

declare(strict_types = 1);

namespace Bakabot\Action;

/** @psalm-suppress PropertyNotSetInConstructor */
final class SendMessage extends AbstractAction
{
    public /* readonly */ string $message;

    public function withMessage(string $message): self
    {
        assert(trim($message) !== '');

        return $this->copy(message: $message);
    }
}
