<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use BadMethodCallException;
use Bakabot\Component\Core\Common\Cloneable;

final class DoNothing implements Action
{
    use Cloneable;

    private /* readonly */ bool $removesTriggerMessage;

    public function __construct(bool $removesTriggerMessage = false)
    {
        $this->removesTriggerMessage = $removesTriggerMessage;
    }

    private function unsupported(): BadMethodCallException
    {
        return new BadMethodCallException(self::class . ' does nothing.');
    }

    public function mentionRecipient(): DoNothing
    {
        throw $this->unsupported();
    }

    public function mentionsRecipient(): bool
    {
        return false;
    }

    public function removeTriggerMessage(bool $removesTriggerMessage = true): DoNothing
    {
        return $this->copy(removesTriggerMessage: $removesTriggerMessage);
    }

    public function removesTriggerMessage(): bool
    {
        return $this->removesTriggerMessage;
    }
}
