<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\User\UserInterface;
use LogicException;

final class SendDirectMessage implements SendMessageInterface
{
    use TriggerMessageTrait;

    private ?array $embed = null;
    private ?string $message = null;
    private UserInterface $recipient;

    public function __construct(
        UserInterface $recipient,
        ?string $message,
        ?array $embed,
    ) {
        if ($message === null && $embed === null) {
            throw new LogicException('You need to set either a message, an embed, or both.');
        }

        $this->embed = $embed;
        $this->message = $message;
        $this->recipient = $recipient;
    }

    public function getEmbed(): ?array
    {
        return $this->embed;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getRecipient(): UserInterface
    {
        return $this->recipient;
    }
}
