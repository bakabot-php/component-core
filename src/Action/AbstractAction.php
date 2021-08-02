<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\TargetInterface;
use Bakabot\Chat\User\UserInterface;

abstract class AbstractAction implements ActionInterface
{
    use TriggerMessageTrait;

    private bool $pingRecipient;
    private TargetInterface $target;

    public function __construct(TargetInterface $target, ?bool $pingRecipient = null)
    {
        if ($pingRecipient === null) {
            $pingRecipient = match (true) {
                ($target instanceof ChannelInterface) => !$target->isPrivate(),
                ($target instanceof MessageInterface) => !$target->getChannel()->isPrivate(),
                ($target instanceof UserInterface) => false,
            };
        }

        $this->pingRecipient = $pingRecipient;
        $this->target = $target;
    }

    final public function getTarget(): TargetInterface
    {
        return $this->target;
    }

    final public function shouldPingRecipient(): bool
    {
        return $this->pingRecipient;
    }
}
