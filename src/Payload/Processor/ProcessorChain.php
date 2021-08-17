<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action;
use Bakabot\Payload\PayloadInterface;
use Throwable;

final class ProcessorChain extends AbstractProcessor
{
    /** @var ProcessorInterface[] */
    private array $processors = [];

    /**
     * @param PayloadInterface $payload
     * @return Promise<Action\ActionInterface>
     */
    public function process(PayloadInterface $payload): Promise
    {
        // The outcome of the processor chain will *at least* always be a null action
        $deferred = new Deferred();
        $resolvedAction = new Action\DoNothing();

        if (count($this->processors) > 0) {
            $currentPayload = $payload;

            foreach ($this->processors as $processor) {
                /** @var PayloadInterface $currentPayload */
                $promise = $processor->process($currentPayload);
                $promise->onResolve(
                    function (?Throwable $ex, Action\ActionInterface|PayloadInterface|null $return) use (
                        &$currentPayload,
                        $promise,
                        &$resolvedAction
                    ): void {
                        Promise\rethrow($promise);

                        match (true) {
                            $return instanceof Action\ActionInterface => $resolvedAction = $return,
                            $return instanceof PayloadInterface => $currentPayload = $return,
                        };
                    }
                );
            }
        }

        /** @var Action\ActionInterface $resolvedAction */
        $resolvedAction->setTriggerMessage($payload->getMessage());

        return $this->action($resolvedAction, $deferred);
    }

    public function push(ProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
    }
}
