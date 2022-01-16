<?php

declare(strict_types = 1);

namespace Bakabot\Processor;

use Amp\Promise;
use Bakabot\Chat\Message\Message;

trait HandlerChain
{
    public function handle(Message $message): Promise
    {
        return parent::handle($message);
    }
}
