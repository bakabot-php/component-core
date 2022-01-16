<?php

declare(strict_types = 1);

namespace Bakabot\Processor;

use Amp\Promise;
use Amp\Success;
use Bakabot\Action\Action;
use Bakabot\Chat\Message\Message;
use Throwable;

abstract class BaseHandler implements Handler
{
    protected ?Handler $next;

    public function handle(Message $message) : Promise
    {
        if (!isset($this->next)) {
            return new Success();
        }

        $promise = $this->next->handle($message);
        $promise->onResolve(
            function (?Throwable $ex, ?Action $action) use ($message) {
                if (!$action && $this->next) {
                    return $this->next->handle($message);
                }

                return null;
            }
        );

        return $promise;
    }

    public function next(Handler $handler) : Handler
    {
        $this->next = $handler;

        return $handler;
    }
}
