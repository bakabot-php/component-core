<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\MessageInterface;

trait TriggerMessageTrait
{
    private bool $deleteTriggerMessage = false;
    private ?MessageInterface $triggerMessage = null;

    public function getTriggerMessage(): ?MessageInterface
    {
        return $this->triggerMessage;
    }

    public function setTriggerMessage(MessageInterface $triggerMessage): self
    {
        $this->triggerMessage = $triggerMessage;

        return $this;
    }

    public function shouldDeleteTriggerMessage(?bool $deleteTriggerMessage = null): bool
    {
        // getter
        if ($deleteTriggerMessage === null) {
            return $this->deleteTriggerMessage;
        }

        // setter
        return $this->deleteTriggerMessage = $deleteTriggerMessage;
    }
}
