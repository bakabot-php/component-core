<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Amp\Promise;
use Bakabot\Environment;

interface Handler
{
    public function environment(): Environment;

    public function handle(Action $action): Promise;
}
