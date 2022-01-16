<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use Bakabot\Chat\Target;

interface User extends Target
{
    public function username(): string;
}
