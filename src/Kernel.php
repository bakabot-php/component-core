<?php

declare(strict_types = 1);

namespace Bakabot;

use Acclimate\Container\ArrayContainer;
use Acclimate\Container\CompositeContainer;
use Amp\Loop\Driver;
use Bakabot\Component\Component;
use Bakabot\Component\Components;
use Bakabot\Component\Core\Amp\Loop\ReloadException;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class Kernel
{
    private bool $compileContainer = false;
    private Components $components;
    private ?CompositeContainer $container = null;
    private ContainerInterface $delegateContainer;

    /**
     * @param Components|Component[] $components
     * @param ContainerInterface|array $delegateContainer
     */
    public function __construct(
        Components|array $components,
        ContainerInterface|array $delegateContainer = []
    ) {
        if (is_array($components)) {
            $components = new Components(...$components);
        }

        $this->components = $components;

        if (is_array($delegateContainer)) {
            $delegateContainer = new ArrayContainer($delegateContainer);
        }

        $this->delegateContainer = $delegateContainer;
    }

    public function __destruct()
    {
        $this->shutdown();
        unset($this->delegateContainer);
    }

    private function buildContainer(ContainerBuilder $containerBuilder): ContainerInterface
    {
        if ($this->compileContainer) {
            $tmpContainer = (clone $containerBuilder)->build();

            /** @var string $cacheDir */
            $cacheDir = $tmpContainer->get('bakabot.dirs.cache');
            $containerBuilder->enableCompilation($cacheDir, 'BakabotContainer');
        }

        return $containerBuilder->build();
    }

    private function prepareContainerBuilder(ContainerInterface $delegateContainer): ContainerBuilder
    {
        /** @var ContainerBuilder $containerBuilder */
        $containerBuilder = $delegateContainer->has(ContainerBuilder::class)
            ? $delegateContainer->get(ContainerBuilder::class)
            : new ContainerBuilder();

        $containerBuilder->addDefinitions(
            [
                self::class => $this,
            ]
        );

        return $containerBuilder;
    }

    public function boot(): ContainerInterface
    {
        if ($this->container !== null) {
            return $this->container;
        }

        $container = new CompositeContainer();
        $container->addContainer($this->delegateContainer);

        $containerBuilder = $this->prepareContainerBuilder($container);

        foreach ($this->components as $component) {
            $component->register($containerBuilder);
        }

        $container->addContainer($this->buildContainer($containerBuilder));

        foreach ($this->components as $component) {
            $component->boot($container);
        }

        return $this->container = $container;
    }

    public function compileContainer(): void
    {
        $this->compileContainer = true;
    }

    public function components(): Components
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

    public function start(?callable $callback = null): void
    {
        reload:
        $container = $this->boot();
        $driver = $container->get(Driver::class);
        $logger = $container->get(LoggerInterface::class);

        if ($callback) {
            $driver->defer($callback);
        }

        try {
            $driver->defer(static fn() => $logger->debug(sprintf('Loop started using %s.', $driver::class)));
            $driver->run();
        } catch (ReloadException) {
            $this->shutdown();
            goto reload;
        }

        $this->shutdown();
    }

    public function shutdown(): void
    {
        if (!isset($this->container)) {
            return;
        }

        $components = array_reverse(iterator_to_array($this->components));

        foreach ($components as $component) {
            $component->shutdown($this->container);
        }

        unset($this->container);
    }

    public function stop(Driver $driver): callable
    {
        return static function (string $watcherId) use ($driver) {
            $driver->cancel($watcherId);
            $driver->stop();
        };
    }
}
