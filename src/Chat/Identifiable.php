<?php

declare(strict_types = 1);

namespace Bakabot\Chat;

use Stringable;

interface Identifiable extends Stringable
{
    public function id(): string;
}
