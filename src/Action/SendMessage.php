<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\User\UserInterface;
use LogicException;

final class SendMessage implements SendMessageInterface
{
    use TriggerMessageTrait;

    private ChannelInterface $channel;
    private ?array $embed = null;
    private ?string $message = null;
    private bool $pingRecipient;
    private ?UserInterface $recipient = null;

    public function __construct(
        ChannelInterface $channel,
        ?string $message,
        ?array $embed,
        ?UserInterface $recipient = null,
        bool $pingRecipient = false
    ) {
        if ($message === null && $embed === null) {
            throw new LogicException('You need to set either a message, an embed, or both.');
        }

        $this->channel = $channel;
        $this->embed = $embed;
        $this->message = $message;
        $this->recipient = $recipient;
        $this->pingRecipient = $pingRecipient && ($this->recipient && !$this->channel->isPrivate());
    }

    public static function withEmbed(
        ChannelInterface $channel,
        array $embed,
        ?UserInterface $recipient = null,
        bool $pingRecipient = false
    ): self {
        return new self($channel, null, $embed, $recipient, $pingRecipient);
    }

    public static function withMessage(
        ChannelInterface $channel,
        string $message,
        ?UserInterface $recipient = null,
        bool $pingRecipient = false
    ): self {
        return new self($channel, $message, null, $recipient, $pingRecipient);
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getEmbed(): ?array
    {
        return $this->embed;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getRecipient(): ?UserInterface
    {
        return $this->recipient;
    }

    public function shouldPingRecipient(): bool
    {
        return $this->pingRecipient;
    }
}
