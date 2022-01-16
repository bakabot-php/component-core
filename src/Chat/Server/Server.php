<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server;

use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Target;

interface Server extends Target
{
    public function defaultChannel(): Channel;

    public function name(): string;
}
