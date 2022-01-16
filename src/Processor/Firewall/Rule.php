<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall;

use Bakabot\Chat\Message\Message;
use Bakabot\Processor\Handler;

interface Rule extends Handler
{
    public const PREFIX_INTERNAL = 'internal:';
    public const PREFIX_SERVER = 'server:';

    public function detailedMessage(Message $message): ?string;

    public function name(): string;
}
