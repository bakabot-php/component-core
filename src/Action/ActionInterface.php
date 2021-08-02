<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\TargetInterface;
use Bakabot\Chat\Message\MessageInterface;

interface ActionInterface
{
    public function deleteTriggerMessage(?bool $deleteTriggerMessage = null): bool;

    public function getTarget(): TargetInterface;

    public function getTriggerMessage(): MessageInterface;

    public function setTriggerMessage(MessageInterface $triggerMessage): void;

    public function shouldPingRecipient(): bool;
}
