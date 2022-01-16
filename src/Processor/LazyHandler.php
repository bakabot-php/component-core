<?php

declare(strict_types = 1);

namespace Bakabot\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Chat\Message\Message;
use Throwable;
use function Amp\call;

/** @template THandler of Handler */
abstract class LazyHandler extends BaseHandler
{
    /** @var callable(Message $message): THandler */
    private $factory;

    /** @param callable(Message $message): THandler $factory */
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
     * @return THandler
     */
    public function getInnerHandler(Message $message): Handler
    {
        /** @var Promise<THandler> $promise */
        return Promise\wait(call($this->factory, $message));
    }

    public function handle(Message $message) : Promise
    {
        $deferred = new Deferred();

        /** @var Promise<?Handler> $promise */
        $promise = call($this->factory, $message);
        Promise\rethrow($promise);

        $promise->onResolve(
            function (?Throwable $ex, ?Handler $handler) use ($deferred, $message) {
                if ($handler instanceof Handler) {
                    $deferred->resolve($handler->handle($message));
                }
            }
        );

        return $promise;
    }
}
