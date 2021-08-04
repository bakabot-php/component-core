<?php

declare(strict_types = 1);

namespace Bakabot;

use Amp\Loop;
use Bakabot\Component\Collection as ComponentCollection;
use Bakabot\Component\ComponentInterface;
use Bakabot\Component\Core\Amp\Loop\RebootException;
use Psr\Container\ContainerInterface;

final class Bot
{
    private Kernel $kernel;

    /**
     * @param ComponentCollection|ComponentInterface[] $components
     * @param ContainerInterface|null $container
     */
    public function __construct(ComponentCollection|array $components, ?ContainerInterface $container = null)
    {
        $this->kernel = new Kernel($components, $container);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->kernel->getContainer();
    }

    public function run(?callable $callback = null): void
    {
        reboot:
        $this->kernel->boot();

        try {
            Loop::run($callback);
        } catch (RebootException) {
            $this->kernel->shutdown();
            goto reboot;
        }

        $this->kernel->shutdown();
        unset($this->kernel);
    }
}
