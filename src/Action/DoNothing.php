<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\TargetInterface;
use LogicException;

final class DoNothing implements ActionInterface
{
    public function deleteTriggerMessage(?bool $deleteTriggerMessage = null): bool
    {
        return false;
    }

    public function getTriggerMessage(): MessageInterface
    {
        throw new LogicException();
    }

    public function setTriggerMessage(MessageInterface $triggerMessage): void
    {

    }

    public function getTarget(): TargetInterface
    {
        throw new LogicException();
    }

    public function shouldPingRecipient(): bool
    {
        return false;
    }
}
