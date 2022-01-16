<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Target;

interface Targeted extends Action
{
    public function target(): Target;
}
