<?php

declare(strict_types = 1);

namespace Bakabot\Bot;

use Amp\Loop;
use Bakabot\BotInterface;
use Bakabot\Component\Collection as ComponentCollection;
use Bakabot\Component\ComponentInterface;
use DI\Container;
use DI\ContainerBuilder;

final class Bot implements BotInterface
{
    private ComponentCollection $components;
    private ?Container $container = null;
    private ContainerBuilder $containerBuilder;

    /**
     * @param ContainerBuilder $containerBuilder
     * @param ComponentInterface[] $components
     */
    public function __construct(ContainerBuilder $containerBuilder, array $components)
    {
        $this->containerBuilder = $containerBuilder;
        $this->components = new ComponentCollection();

        foreach ($components as $component) {
            $this->components->push($component);

            $component->register($this->containerBuilder);
        }
    }

    public function getContainer(): Container
    {
        if ($this->container === null) {
            $container = $this->containerBuilder->build();

            foreach ($this->components as $component) {
                $component->boot($container);
            }

            $this->container = $container;
        }

        return $this->container;
    }

    public function run(): void
    {
        $container = $this->getContainer();

        foreach (array_reverse(iterator_to_array($this->components)) as $component) {
            /** @var ComponentInterface $component */
            $component->shutdown($container);
        }

        unset($this->container);
    }
}
