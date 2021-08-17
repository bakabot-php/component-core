<?php

declare(strict_types = 1);

namespace Bakabot;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class Bot
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->kernel->boot();
    }

    public function run(?callable $callback = null): void
    {
        $container = $this->kernel->boot();

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);
        $logger->debug('Kernel booted.');

        try {
            $logger->info('Starting kernel...');
            $this->kernel->start($callback);
        } finally {
            $logger->debug('Shutting down...');
            $this->kernel->shutdown();
            $logger->debug('Shutdown complete.');
            unset($container, $logger, $this->kernel);
        }
    }
}
