<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\Container;
use DI\ContainerBuilder;

abstract class AbstractComponent implements ComponentInterface
{
    final public function __construct()
    {
    }

    abstract protected function getParameters(): array;

    abstract protected function getServices(): array;

    public function boot(Container $container): void
    {
    }

    public function register(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions($this->getParameters(), $this->getServices());
    }

    public function shutdown(Container $container): void
    {
    }
}
