<?php

declare(strict_types = 1);

namespace Bakabot\Processor;

use Amp\Promise;
use Bakabot\Chat\Message\Message;

interface Handler
{
    public function next(Handler $handler): Handler;

    public function handle(Message $message): Promise;
}
