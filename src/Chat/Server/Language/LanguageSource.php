<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Language;

use Stringable;

interface LanguageSource extends Stringable
{
    public function language(): Language;
}
