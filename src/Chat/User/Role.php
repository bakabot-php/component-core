<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use Bakabot\Chat\Identifiable;

interface Role extends Identifiable
{
    public function id(): string;
}
