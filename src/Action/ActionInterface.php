<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\MessageInterface;

interface ActionInterface
{
    public function getTriggerMessage(): ?MessageInterface;

    public function setTriggerMessage(MessageInterface $triggerMessage): self;

    public function shouldDeleteTriggerMessage(?bool $deleteTriggerMessage = null): bool;
}
