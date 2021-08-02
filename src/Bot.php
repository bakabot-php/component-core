<?php

declare(strict_types = 1);

namespace Bakabot;

use Bakabot\Component\Bootstrapper;
use Bakabot\Component\Collection as ComponentCollection;
use Bakabot\Component\ComponentInterface;
use Psr\Container\ContainerInterface;

final class Bot
{
    private Bootstrapper $bootstrapper;

    /**
     * @param ComponentCollection|ComponentInterface[] $components
     * @param ContainerInterface|null $container
     */
    public function __construct(ComponentCollection|array $components, ?ContainerInterface $container = null)
    {
        $this->bootstrapper = new Bootstrapper($components, $container);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->bootstrapper->getContainer();
    }

    public function run(): void
    {
        $container = $this->getContainer();

        // start the loop, blahblah

        unset($this->bootstrapper);
    }
}
