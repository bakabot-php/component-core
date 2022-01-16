<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Command\Trigger\Trigger;

final class Payload
{
    /**
     * @param string[] $parsedArguments
     */
    public function __construct(
        public /* readonly */ Trigger $trigger,
        public /* readonly */ string $commandName,
        public /* readonly */ string $rawArguments,
        public /* readonly */ array $parsedArguments
    ) {
    }
}
