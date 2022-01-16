<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Target;
use Bakabot\Component\Core\Common\Cloneable;

abstract class Reply
{
    use Cloneable;

    public /* readonly */ Message $message;
    public /* readonly */ Target $target;

    final public function __construct(Message $message)
    {
        $this->message = $message;
        $this->target = $message->channel;
    }

    final public function inPrivate(): static
    {
        return $this->toTarget($this->message->author);
    }

    final public function toTarget(Target $target): static
    {
        return $this->copy(target: $target);
    }

    /**
     * @param string[] $files
     * @return SendFiles
     */
    final public function withFiles(array $files): SendFiles
    {
        return SendFiles::to($this->target)->withFiles($files);
    }

    final public function withMessage(string $message): SendMessage
    {
        return SendMessage::to($this->target)->withMessage($message);
    }

    final public function withTemplate(string $template, array $context): SendTemplatedMessage
    {
        return SendTemplatedMessage::to($this->target)->withTemplate($template, $context);
    }
}
