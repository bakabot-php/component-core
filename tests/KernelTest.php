<?php

declare(strict_types = 1);

namespace Bakabot;

use Acclimate\Container\ArrayContainer;
use Bakabot\Component\Collection;
use Bakabot\Component\ComponentInterface;
use Bakabot\Component\CoreComponent;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/** @backupGlobals  */
class KernelTest extends TestCase
{
    /** @test */
    public function allows_access_to_components(): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $components = [$component];
        $kernel = new Kernel($components);

        self::assertEquals(new Collection($components), $kernel->getComponents());
    }

    /** @test */
    public function produced_container_has_access_to_parent_dependencies(): void
    {
        $components = [new CoreComponent()];
        $kernel = new Kernel($components);

        $container = $kernel->getContainer();

        self::assertTrue($container->has(Connection::class));

        // overriden, originally from component-database
        self::assertTrue($container->has('bakabot.db.config'));
        self::assertSame('sqlite:///app/var/database.sqlite', $container->get('bakabot.db.config')['url']);
    }

    /** @test */
    public function produced_container_can_optionally_wrap_a_top_level_container(): void
    {
        $wrappedContainer = new ArrayContainer();
        $wrappedContainer[ContainerBuilder::class] = new ContainerBuilder();
        $wrappedContainer[LoggerInterface::class] = new NullLogger();

        $components = [new CoreComponent()];
        $kernel = new Kernel($components, $wrappedContainer);

        $container = $kernel->getContainer();

        self::assertTrue($container->has(LoggerInterface::class));
        self::assertInstanceOf(NullLogger::class, $container->get(LoggerInterface::class));
    }

    /** @test */
    public function components_are_booted_and_shutdown_only_once(): void
    {
        $component = $this->createMock(ComponentInterface::class);
        $component
            ->expects($this->once())
            ->method('boot');

        $component
            ->expects($this->once())
            ->method('shutdown')
        ;

        $kernel = new Kernel([$component]);

        self::assertSame($kernel->boot(), $kernel->getContainer());

        $kernel->shutdown();
        $kernel->shutdown();
    }
}
