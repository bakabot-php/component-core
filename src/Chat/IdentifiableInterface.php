<?php

declare(strict_types = 1);

namespace Bakabot\Chat;

use Stringable;

interface IdentifiableInterface extends Stringable
{
    public function getId(): string;
}
