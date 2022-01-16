<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action;
use Bakabot\Chat\Message\Message;
use Throwable;

final class ProcessorChain extends AbstractProcessor
{
    /** @var Processor[] */
    private array $processors = [];

    /**
     * @param Message $contents
     * @return Promise<Message|Action\Action>
     */
    public function process(Message $contents): Promise
    {
        // The outcome of the processor chain will *at least* always be a null action
        $deferred = new Deferred();
        $resolvedAction = new Action\DoNothing();

        if (count($this->processors) > 0) {
            $currentMessage = $contents;

            foreach ($this->processors as $processor) {
                /** @var Message $currentMessage */
                $promise = $processor->process($currentMessage);
                $promise->onResolve(
                    function (?Throwable $ex, Action\Action|Message|null $return) use (
                        &$currentPayload,
                        $promise,
                        &$resolvedAction
                    ): void {
                        Promise\rethrow($promise);

                        match (true) {
                            $return instanceof Action\Action => $resolvedAction = $return,
                            $return instanceof Message => $currentPayload = $return,
                        };
                    }
                );
            }
        }

        /** @var Action\AbstractAction $resolvedAction */
        $resolvedAction->setTriggerMessage($payload->getMessage());

        return $this->action($resolvedAction, $deferred);
    }

    public function push(Processor $processor): void
    {
        $this->processors[] = $processor;
    }
}
