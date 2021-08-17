<?php

declare(strict_types = 1);

namespace Bakabot;

use Acclimate\Container\ArrayContainer;
use Acclimate\Container\CompositeContainer;
use Amp\Loop;
use Amp\Loop\Driver;
use Bakabot\Component\Collection as ComponentCollection;
use Bakabot\Component\Component;
use Bakabot\Component\Core\Amp\Loop\ReloadException;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class Kernel implements KernelInterface
{
    private ComponentCollection $components;
    private ?CompositeContainer $container = null;
    private bool $enableContainerCompilation = false;
    private ContainerInterface $wrappedContainer;

    /**
     * @param ComponentCollection|Component[] $components
     * @param ContainerInterface|array $wrappedContainer
     */
    public function __construct(
        ComponentCollection|array $components,
        ContainerInterface|array $wrappedContainer = []
    ) {
        if (is_array($components)) {
            $components = new ComponentCollection($components);
        }

        $this->components = $components;

        if (is_array($wrappedContainer)) {
            $wrappedContainer = new ArrayContainer($wrappedContainer);
        }

        $this->wrappedContainer = $wrappedContainer;
    }

    private function compileContainer(ContainerBuilder $containerBuilder): void
    {
        if ($this->enableContainerCompilation === false) {
            return;
        }

        $tmpContainer = (clone $containerBuilder)->build();

        /** @var string $cacheDir */
        $cacheDir = $tmpContainer->get('bakabot.dirs.cache');
        $containerBuilder->enableCompilation($cacheDir);

        unset($tmpContainer);
    }

    private function prepareContainerBuilder(CompositeContainer $container): ContainerBuilder
    {
        /** @var ContainerBuilder $containerBuilder */
        $containerBuilder = $container->has(ContainerBuilder::class)
            ? $container->get(ContainerBuilder::class)
            : new ContainerBuilder()
        ;

        $containerBuilder->addDefinitions(
            [
                self::class => $this,
            ]
        );

        return $containerBuilder;
    }

    public function __destruct()
    {
        $this->shutdown();
        unset($this->container, $this->wrappedContainer);
    }

    public function boot(): ContainerInterface
    {
        if ($this->container !== null) {
            return $this->container;
        }

        $container = new CompositeContainer();
        $container->addContainer($this->wrappedContainer);

        $containerBuilder = $this->prepareContainerBuilder($container);

        foreach ($this->components as $component) {
            $component->register($containerBuilder);
        }

        $this->compileContainer($containerBuilder);

        $container->addContainer($containerBuilder->build());

        foreach ($this->components as $component) {
            $component->boot($container);
        }

        return $this->container = $container;
    }

    public function enableContainerCompilation(): void
    {
        $this->enableContainerCompilation = true;
    }

    public function getComponents(): ComponentCollection
    {
        return $this->components;
    }

    public function reload(Driver $driver): callable
    {
        return static function () use ($driver) {
            $driver->stop();
            throw new ReloadException();
        };
    }

    public function stop(Driver $driver): callable
    {
        return static function (string $watcherId) use ($driver) {
            $driver->cancel($watcherId);
            $driver->stop();
        };
    }

    public function start(?callable $callback = null): void
    {
        reload:
        $container = $this->boot();

        /** @var Driver $driver */
        $driver = $container->get(Loop::class);

        if ($callback) {
            $driver->defer($callback);
        }

        try {
            $driver->defer(static function () use ($container, $driver) {
                /** @var LoggerInterface $logger */
                $logger = $container->get(LoggerInterface::class);
                $logger->debug(sprintf('Loop started using %s.', $driver::class));
            });
            $driver->run();
        } catch (ReloadException) {
            $this->shutdown();
            goto reload;
        }

        $this->shutdown();
    }

    public function shutdown(): void
    {
        if ($this->container === null) {
            return;
        }

        $components = array_reverse(iterator_to_array($this->components));

        foreach ($components as $component) {
            $component->shutdown($this->container);
        }

        $this->container = null;
    }
}
