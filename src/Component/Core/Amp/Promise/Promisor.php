<?php

declare(strict_types = 1);

namespace Bakabot\Component\Core\Amp\Promise;

use Amp\Deferred;
use Amp\Promise;
use Amp\Success;
use Bakabot\Action\Action;
use Bakabot\Chat\Message\Message;
use Bakabot\Command\Payload;

trait Promisor
{
    /**
     * @param Action $action
     * @param Deferred|null $deferred
     * @return Promise<Action>
     */
    final protected function action(Action $action, ?Deferred $deferred = null): Promise
    {
        if ($deferred !== null) {
            $deferred->resolve($action);
            return $deferred->promise();
        }

        return new Success($action);
    }

    /**
     * @param Deferred $deferred
     * @return Promise<Action|Message|Payload>
     */
    final protected function defer(Deferred $deferred): Promise
    {
        return $deferred->promise();
    }

    /**
     * @param Message $message
     * @param Deferred|null $deferred
     * @return Promise<Message>
     */
    final protected function message(Message $message, ?Deferred $deferred = null): Promise
    {
        if ($deferred !== null) {
            $deferred->resolve($message);
            return $deferred->promise();
        }

        return new Success($message);
    }

    /**
     * @param Action|Message|Payload|Promise<Action|Message|Payload> $value
     * @param Deferred|null $deferred
     * @return Promise<Action|Message|Payload>
     */
    final protected function promise(
        Action|Message|Payload|Promise $value,
        ?Deferred $deferred = null
    ): Promise {
        if ($value instanceof Action) {
            return $this->action($value, $deferred);
        }

        if ($value instanceof Message) {
            return $this->message($value, $deferred);
        }

        /** @var Promise<Action|Message|Payload> $value */
        if ($deferred !== null) {
            $deferred->resolve($value);
            return $deferred->promise();
        }

        return $value;
    }
}
