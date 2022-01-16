<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Channel;

use Bakabot\Chat\Server\Server;

interface ServerChannel extends Channel
{
    public function server(): Server;
}
