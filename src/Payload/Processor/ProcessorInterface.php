<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Promise;
use Bakabot\Action\ActionInterface;
use Bakabot\Payload\PayloadInterface;

interface ProcessorInterface
{
    /**
     * Passes along the payload (and is able to return a decorated one).
     * Will eventually resolve to an @see ActionInterface.
     *
     * @param PayloadInterface $payload
     * @return Promise<PayloadInterface|ActionInterface>
     */
    public function process(PayloadInterface $payload): Promise;
}
