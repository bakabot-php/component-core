<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Amp\Promise;
use Bakabot\Action\Action;
use Stringable;

interface Command extends Stringable
{
    public function argumentExpression(): string;

    public function description(): string;

    public function helpText(): string;

    public function name(): string;

    /** @return string[] */
    public function supportedEnvironments(): array;

    /** @return Promise<Action> */
    public function run(): Promise;
}
