<?php

declare(strict_types = 1);

namespace Bakabot;

use Acclimate\Container\ArrayContainer;
use Acclimate\Container\CompositeContainer;
use Bakabot\Component\Collection as ComponentCollection;
use Bakabot\Component\ComponentInterface;
use Bakabot\Component\Core\Amp\Loop\RebootException;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final class Kernel
{
    private ComponentCollection $components;
    private ?CompositeContainer $container = null;
    private ContainerInterface $wrappedContainer;

    /**
     * @param ComponentCollection|ComponentInterface[] $components
     * @param ContainerInterface|null $wrappedContainer
     */
    public function __construct(ComponentCollection|array $components, ?ContainerInterface $wrappedContainer = null)
    {
        if (is_array($components)) {
            $components = new ComponentCollection($components);
        }

        $this->components = $components;
        $this->wrappedContainer = $wrappedContainer ?? new ArrayContainer();
    }

    private function prepareContainerBuilder(CompositeContainer $container): ContainerBuilder
    {
        $containerBuilder = $container->has(ContainerBuilder::class)
            ? $container->get(ContainerBuilder::class)
            : new ContainerBuilder()
        ;

        $containerBuilder->addDefinitions(
            [
                ContainerBuilder::class => static fn () => $containerBuilder,
                Kernel::class => fn () => $this,
            ]
        );

        $containerBuilder->wrapContainer($container);

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

       $container->addContainer($containerBuilder->build());

        foreach ($this->components as $component) {
            $component->boot($container);
        }

        return $this->container = $container;
    }

    public function getComponents(): ComponentCollection
    {
        return $this->components;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->boot();
    }

    public function reboot(): void
    {
        throw new RebootException();
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
