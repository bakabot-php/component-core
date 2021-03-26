<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
    /** @test */
    public function registers_services_and_parameters(): void
    {
        $containerBuilder = new ContainerBuilder();

        $component = new DependencyDummy();
        $component->register($containerBuilder);

        $container = $containerBuilder->build();
        self::assertTrue($container->has('hello'));
        self::assertTrue($container->has('my_service'));
    }
}
