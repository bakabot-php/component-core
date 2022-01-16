<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Processor;

use Amp\Promise;
use Bakabot\Action\Action;
use Bakabot\Chat\Message\Message;

interface Processor
{
    /**
     * Passes along the message (and is able to return a decorated one).
     *
     * @param Message $contents
     * @return Promise<Message|Action>
     */
    public function process(Message $contents): Promise;
}
