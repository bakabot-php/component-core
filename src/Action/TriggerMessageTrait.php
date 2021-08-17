<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\MessageInterface;
use LogicException;

trait TriggerMessageTrait
{
    private bool $deleteTriggerMessage = false;
    private ?MessageInterface $triggerMessage = null;

    final public function deleteTriggerMessage(?bool $deleteTriggerMessage = null): bool
    {
        // no argument - getter
        if ($deleteTriggerMessage === null) {
            return $this->deleteTriggerMessage;
        }

        // with argument - setter
        return $this->deleteTriggerMessage = $deleteTriggerMessage;
    }

    final public function getTriggerMessage(): MessageInterface
    {
        assert($this->triggerMessage !== null);

        return $this->triggerMessage;
    }

    final public function setTriggerMessage(MessageInterface $triggerMessage): void
    {
        $this->triggerMessage = $triggerMessage;
    }
}
