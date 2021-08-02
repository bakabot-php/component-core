<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\TargetInterface;
use Bakabot\Chat\User\UserInterface;
use DateTimeInterface;

interface MessageInterface extends TargetInterface
{
    public function getAuthor(): UserInterface;

    public function getChannel(): ChannelInterface;

    public function getContent(): string;

    public function getCreationTime(): DateTimeInterface;

    public function getEditedTime(): ?DateTimeInterface;

    public function getId(): string;

    public function wasEdited(): bool;
}
