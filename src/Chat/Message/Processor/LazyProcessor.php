<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action\Action;
use Bakabot\Chat\Message\Message;
use Throwable;
use function Amp\call;

/** @template TProcessor of Processor */
final class LazyProcessor extends AbstractProcessor
{
    /** @var callable(Message $message): TProcessor */
    private $factory;

    /** @param callable(Message $message): TProcessor $factory */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @internal
     *
     * Only used for testing, to expose the inner processor.
     *
     * @param Message $message
     * @return TProcessor
     */
    public function getInnerProcessor(Message $message): Processor
    {
        /** @var Promise<TProcessor> $promise */
        return Promise\wait(call($this->factory, $message));
    }

    /**
     * @param Message $contents
     * @return Promise<Message|Action>
     */
    public function process(Message $contents): Promise
    {
        $deferred = new Deferred();

        /** @var Promise<Message|Action> $promise */
        $promise = call($this->factory, $contents);
        $promise->onResolve(
            function (?Throwable $ex, ?Processor $processor) use ($deferred, $contents, $promise) {
                /** @var TProcessor|null $processor */
                if (isset($processor)) {
                    $deferred->resolve($processor->process($contents));
                }

                Promise\rethrow($promise);
            }
        );

        return $this->defer($deferred);
    }
}
