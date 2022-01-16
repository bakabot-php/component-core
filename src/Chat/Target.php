<?php

declare(strict_types = 1);

namespace Bakabot\Chat;

use Bakabot\Environment;

interface Target extends Identifiable
{
    public function environment(): Environment;
}
