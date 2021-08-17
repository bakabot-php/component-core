<?php

declare(strict_types = 1);

namespace Bakabot;

use Acclimate\Container\ArrayContainer;
use Amp\Loop;
use Amp\Loop\Driver;
use Bakabot\Component\Collection;
use Bakabot\Component\Component;
use Bakabot\Component\Core\Amp\Loop\ReloadException;
use Bakabot\Component\CoreComponent;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class KernelTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('APP_DEBUG=true');
        putenv('APP_DIR=/tmp');
        putenv('APP_ENV=test');
    }

    private function createKernel(array $components): Kernel
    {
        $container = new ArrayContainer(
            [
                // to keep logs clean
                LoggerInterface::class => new NullLogger()
            ]
        );

        return new Kernel($components, $container);
    }

    /** @test */
    public function wraps_array_as_wrapped_container(): void
    {
        $kernel = new Kernel([], ['hello' => 'World']);

        $container = $kernel->boot();
        self::assertSame('World', $container->get('hello'));
    }

    /** @test */
    public function allows_access_to_components(): void
    {
        $component = $this->createMock(Component::class);
        $components = [$component];
        $kernel = $this->createKernel($components);

        self::assertEquals(new Collection($components), $kernel->getComponents());
    }

    /** @test */
    public function produced_container_can_optionally_wrap_a_top_level_container(): void
    {
        $wrappedContainer = new ArrayContainer();
        $wrappedContainer[ContainerBuilder::class] = new ContainerBuilder();
        $wrappedContainer[LoggerInterface::class] = new NullLogger();

        $components = [new CoreComponent()];
        $kernel = new Kernel($components, $wrappedContainer);

        $container = $kernel->boot();

        self::assertTrue($container->has(LoggerInterface::class));
        self::assertInstanceOf(NullLogger::class, $container->get(LoggerInterface::class));
    }

    /** @test */
    public function can_compile_the_resulting_container(): void
    {
        $kernel = $this->createKernel([new CoreComponent()]);
        $kernel->enableContainerCompilation();
        $kernel->boot();

        self::assertDirectoryExists('/tmp/var/test/cache');
        self::assertFileExists('/tmp/var/test/cache/CompiledContainer.php');
    }

    /** @test */
    public function components_are_booted_and_shutdown_in_correct_order(): void
    {
        $bootOrder = [];
        $shutdownOrder = [];

        $component1 = $this->createMock(Component::class);
        $component1->method('__toString')->willReturn('component1');
        $component1->expects($this->once())->method('boot')->willReturnCallback(function () use (&$bootOrder) {
            $bootOrder[] = 'component1';
        });
        $component1->expects($this->once())->method('shutdown')->willReturnCallback(function () use (&$shutdownOrder) {
            $shutdownOrder[] = 'component1';
        });
        $component2 = $this->createMock(Component::class);

        $component1->method('__toString')->willReturn('component2');
        $component2->expects($this->once())->method('boot')->willReturnCallback(function () use (&$bootOrder) {
            $bootOrder[] = 'component2';
        });
        $component2->expects($this->once())->method('shutdown')->willReturnCallback(function () use (&$shutdownOrder) {
            $shutdownOrder[] = 'component2';
        });

        $kernel = $this->createKernel([$component1, $component2]);

        $kernel->boot();
        $kernel->boot();
        self::assertSame(['component1', 'component2'], $bootOrder);

        $kernel->shutdown();
        $kernel->shutdown();
        self::assertSame(['component2', 'component1'], $shutdownOrder);
    }

    /** @test */
    public function reload_callback_stops_driver_and_throws(): void
    {
        $this->expectException(ReloadException::class);

        $kernel = $this->createKernel([]);

        $driver = $this->createMock(Driver::class);
        $driver->expects($this->once())->method('stop');

        $kernel->reload($driver)();
    }

    /** @test */
    public function stop_callback_will_kill_the_loop(): void
    {
        $kernel = $this->createKernel([]);

        $driver = $this->createMock(Driver::class);
        $driver->expects($this->once())->method('cancel')->with('1234');
        $driver->expects($this->once())->method('stop');

        $kernel->stop($driver)('1234');
    }

    /** @test */
    public function start_starts_the_loop(): void
    {
        $started = false;

        $kernel = $this->createKernel([new CoreComponent()]);
        $kernel->start(function () use (&$started) {
            $started = true;
            Loop::stop();
        });

        self::assertTrue($started);
    }

    /** @test */
    public function start_will_reload_if_triggered(): void
    {
        $iterations = 0;
        $expectedIterations = 3;

        $kernel = $this->createKernel([new CoreComponent()]);
        $kernel->start(function () use (&$iterations, $expectedIterations) {
            if ($iterations > 1000) {
                $this->fail('Too many loops');
            }

            while (++$iterations < $expectedIterations) {
                throw new ReloadException();
            }

            Loop::stop();
        });

        self::assertSame($expectedIterations, $iterations);
    }
}
