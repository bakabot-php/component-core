<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\Container;
use DI\ContainerBuilder;

interface ComponentInterface
{
    public function __construct();

    public function boot(Container $container): void;

    public function register(ContainerBuilder $containerBuilder): void;

    public function shutdown(Container $container): void;
}
