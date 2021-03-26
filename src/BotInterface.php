<?php

declare(strict_types = 1);

namespace Bakabot;

use Psr\Container\ContainerInterface;

interface BotInterface
{
    public function getContainer(): ContainerInterface;

    public function run(): void;
}
