<?php

declare(strict_types = 1);

namespace Bakabot;

use Acclimate\Container\ArrayContainer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class BotTest extends TestCase
{
    /** @test */
    public function getContainer_boots_kernel(): void
    {
        $kernel = $this->createMock(Kernel::class);
        $kernel->expects($this->once())->method('boot');

        $bot = new Bot($kernel);
        $bot->container();
    }

    /** @test */
    public function executes_kernel_lifecycle(): void
    {
        $container = new ArrayContainer([LoggerInterface::class => new NullLogger()]);

        $kernel = $this->createMock(Kernel::class);
        $kernel->expects($this->once())->method('boot')->willReturn($container);
        $kernel->expects($this->once())->method('start');
        $kernel->expects($this->once())->method('shutdown');

        $bot = new Bot($kernel);
        $bot->run();
    }
}
