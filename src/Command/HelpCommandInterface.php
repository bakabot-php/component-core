<?php

declare(strict_types = 1);

namespace Bakabot\Command;

interface HelpCommandInterface extends CommandInterface
{
    public const NAME = 'help';

    public function isFallback(): bool;
}
