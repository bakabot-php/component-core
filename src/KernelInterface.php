<?php

declare(strict_types = 1);

namespace Bakabot;

use Bakabot\Component\Collection as ComponentCollection;
use Psr\Container\ContainerInterface;

interface KernelInterface
{
    public function boot(): ContainerInterface;

    public function enableContainerCompilation(): void;

    public function getComponents(): ComponentCollection;

    public function start(?callable $callback = null): void;

    public function shutdown(): void;
}
