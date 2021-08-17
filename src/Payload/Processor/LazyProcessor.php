<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action\ActionInterface;
use Bakabot\Payload\PayloadInterface;
use Throwable;
use function Amp\call;

/** @template TProcessor of ProcessorInterface */
final class LazyProcessor extends AbstractProcessor
{
    /** @var callable(PayloadInterface $payload): TProcessor */
    private $factory;

    /** @param callable(PayloadInterface $payload): TProcessor $factory */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Only used for testing, to expose the inner processor.
     *
     * @internal
     *
     * @param PayloadInterface $payload
     * @return TProcessor
     */
    public function getInnerProcessor(PayloadInterface $payload): ProcessorInterface
    {
        /** @var Promise<TProcessor> $promise */
        return Promise\wait(call($this->factory, $payload));
    }

    /**
     * @param PayloadInterface $payload
     * @return Promise<PayloadInterface|ActionInterface>
     */
    public function process(PayloadInterface $payload): Promise
    {
        $deferred = new Deferred();

        /** @var Promise<PayloadInterface|ActionInterface> $promise */
        $promise = call($this->factory, $payload);
        $promise->onResolve(
            function (?Throwable $ex, ?ProcessorInterface $processor) use ($deferred, $payload, $promise) {
                /** @var TProcessor|null $processor */
                if (isset($processor)) {
                    $deferred->resolve($processor->process($payload));
                }

                Promise\rethrow($promise);
            }
        );

        return $this->defer($deferred);
    }
}
