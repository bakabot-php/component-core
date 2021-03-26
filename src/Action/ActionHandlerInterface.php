<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Amp\Promise;
use Bakabot\EnvironmentInterface;

interface ActionHandlerInterface
{
    public function getEnvironment(): EnvironmentInterface;

    public function handle(ActionInterface $action): Promise;
}
