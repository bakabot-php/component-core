<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\TargetInterface;

final class SendTemplatedMessage extends AbstractAction implements SendMessageInterface
{
    private array $context;
    private string $message;

    public function __construct(TargetInterface $target, string $message, array $context, ?bool $pingRecipient = null)
    {
        parent::__construct($target, $pingRecipient);

        $this->context = $context;
        $this->message = $message;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
