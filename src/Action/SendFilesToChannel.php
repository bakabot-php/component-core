<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\User\UserInterface;

final class SendFilesToChannel implements SendFilesInterface
{
    use TriggerMessageTrait;

    private ChannelInterface $channel;
    private array $files;
    private bool $mentionRecipient;
    private ?UserInterface $recipient = null;

    public function __construct(
        ChannelInterface $channel,
        array $files,
        ?UserInterface $recipient = null,
        bool $mentionRecipient = false
    ) {
        $this->channel = $channel;
        $this->files = $files;
        $this->recipient = $recipient;
        $this->mentionRecipient = $mentionRecipient && ($this->recipient && !$this->channel->isPrivate());
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getRecipient(): ?UserInterface
    {
        return $this->recipient;
    }

    public function shouldMentionRecipient(): bool
    {
        return $this->mentionRecipient;
    }
}
