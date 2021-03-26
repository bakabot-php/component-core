<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Action\ActionInterface;

interface CommandInterface
{
    public function getArgumentExpression(): string;

    public function getDescription(): string;

    public function getHelpText(): string;

    public function getName(): string;

    /**
     * @return string[]
     */
    public function getSupportedEnvironments(): array;

    public function run(): ActionInterface;
}
