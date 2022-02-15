<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action;
use Bakabot\Payload\PayloadInterface;
use Throwable;

use function Amp\call;

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
        return call(function () use ($payload) {
            if (count($this->processors) > 0) {
                $currentPayload = $payload;

                foreach ($this->processors as $processor) {
                    /** @var PayloadInterface $currentPayload */
                    $return = yield $processor->process($currentPayload);

                    if ($return instanceof Action\ActionInterface) {
                        $resolvedAction = $return;
                        break;
                    }

                    assert($return instanceof PayloadInterface);
                    $currentPayload = $return;
                }
            }

            $resolvedAction ??= new Action\DoNothing();

            /** @var Action\ActionInterface $resolvedAction */
            $resolvedAction->setTriggerMessage($payload->getMessage());

            return $this->action($resolvedAction);
        });
    }

    public function push(ProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
    }
}
