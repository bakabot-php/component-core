<?php

declare(strict_types = 1);

namespace Bakabot\Component\Core\Amp\Promise;

use Amp\Deferred;
use Amp\Promise;
use Amp\Success;
use Bakabot\Action\ActionInterface;
use Bakabot\Payload\PayloadInterface;

trait Promisor
{
    /**
     * @param ActionInterface $action
     * @param Deferred|null $deferred
     * @return Promise<ActionInterface>
     */
    final protected function action(ActionInterface $action, ?Deferred $deferred = null): Promise
    {
        if ($deferred !== null) {
            $deferred->resolve($action);

            /** @var Promise<ActionInterface> $deferredPromise */
            $deferredPromise = $deferred->promise();
            return $deferredPromise;
        }

        /** @var Promise<ActionInterface> $success */
        $success = new Success($action);
        return $success;
    }

    /**
     * @param Deferred $deferred
     * @return Promise<ActionInterface|PayloadInterface>
     */
    final protected function defer(Deferred $deferred): Promise
    {
        /** @var Promise<ActionInterface|PayloadInterface> $deferredPromise */
        $deferredPromise = $deferred->promise();
        return $deferredPromise;
    }

    /**
     * @param PayloadInterface $payload
     * @param Deferred|null $deferred
     * @return Promise<PayloadInterface>
     */
    final protected function payload(PayloadInterface $payload, ?Deferred $deferred = null): Promise
    {
        if ($deferred !== null) {
            $deferred->resolve($payload);

            /** @var Promise<PayloadInterface> $deferredPromise */
            $deferredPromise = $deferred->promise();
            return $deferredPromise;
        }

        /** @var Promise<PayloadInterface> $success */
        $success = new Success($payload);
        return $success;
    }

    /**
     * @param ActionInterface|PayloadInterface|Promise<ActionInterface|PayloadInterface> $value
     * @param Deferred|null $deferred
     * @return Promise<ActionInterface|PayloadInterface>
     */
    final protected function promise(
        ActionInterface|PayloadInterface|Promise $value,
        ?Deferred $deferred = null
    ): Promise {
        if ($value instanceof ActionInterface) {
            return $this->action($value, $deferred);
        }

        if ($value instanceof PayloadInterface) {
            return $this->payload($value, $deferred);
        }

        /** @var Promise<ActionInterface|PayloadInterface> $value */
        if ($deferred !== null) {
            $deferred->resolve($value);

            /** @var Promise<ActionInterface|PayloadInterface> $deferredPromise */
            $deferredPromise = $deferred->promise();
            return $deferredPromise;
        }

        return $value;
    }
}
