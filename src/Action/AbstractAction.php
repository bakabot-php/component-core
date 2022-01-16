<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Channel\PrivateChannel;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Server\Server;
use Bakabot\Chat\Target;
use Bakabot\Chat\User\User;
use Bakabot\Component\Core\Common\Cloneable;

abstract class AbstractAction implements Targeted
{
    use Cloneable;

    public /* readonly */ bool $mentionsRecipient = false;
    public /* readonly */ bool $removesTriggerMessage = false;
    public /* readonly */ Target $target;

    public function __construct(
        Target $target,
        ?bool $mentionsRecipient = null,
        ?bool $removesTriggerMessage = null
    ) {
        if ($mentionsRecipient === null) {
            $mentionsRecipient = match (true) {
                ($target instanceof Channel) => !($target instanceof PrivateChannel),
                ($target instanceof Message) => !($target->channel instanceof PrivateChannel),
                ($target instanceof Server) => true,
                ($target instanceof User) => false,
            };
        }

        if ($removesTriggerMessage === null) {
            $removesTriggerMessage = $mentionsRecipient;
        }

        $this->mentionsRecipient = $mentionsRecipient;
        $this->removesTriggerMessage = $removesTriggerMessage;
        $this->target = clone $target;
    }

    final public static function to(Target $target): static
    {
        return new static($target);
    }

    final public function mentionRecipient(): static
    {
        return $this->copy(mentionsRecipient: true);
    }

    final public function mentionsRecipient(): bool
    {
        return $this->mentionsRecipient;
    }

    final public function removeTriggerMessage(bool $remove = true): static
    {
        return $this->copy(removesTriggerMessage: true);
    }

    final public function removesTriggerMessage(): bool
    {
        return $this->removesTriggerMessage;
    }

    public function target(): Target
    {
        return $this->target;
    }
}
