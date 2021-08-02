<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\User\UserInterface;
use DateTimeInterface;

final class IncomingMessage implements MessageInterface
{
    private UserInterface $author;
    private ChannelInterface $channel;
    private string $content;
    private DateTimeInterface $creationTime;
    private ?DateTimeInterface $editedTime;
    private string $id;

    public function __construct(
        string $id,
        string $content,
        ChannelInterface $channel,
        UserInterface $author,
        DateTimeInterface $creationTime,
        ?DateTimeInterface $editedTime,
    ) {
        $this->author = $author;
        $this->channel = $channel;
        $this->content = trim($content);
        $this->creationTime = $creationTime;
        $this->editedTime = $editedTime;
        $this->id = $id;
    }

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreationTime(): DateTimeInterface
    {
        return $this->creationTime;
    }

    public function getEditedTime(): ?DateTimeInterface
    {
        return $this->editedTime;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function wasEdited(): bool
    {
        return $this->editedTime !== null;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
