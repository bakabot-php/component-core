<?php

declare(strict_types = 1);

namespace Bakabot\Bot;

use Bakabot\Component\AbstractComponent;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class BotTest extends TestCase
{
    /** @test */
    public function registers_given_components(): void
    {
        $component = $this
            ->getMockBuilder(AbstractComponent::class)
            ->onlyMethods(['register'])
            ->getMockForAbstractClass();

        $component->expects(self::once())->method('register');

        new Bot(new ContainerBuilder(), [$component]);
    }

    /** @test */
    public function boot_called_in_registration_order_on_container_build(): void
    {
        $bootOrder = [];
        $bootCallback = function () use (&$bootOrder) {
            $bootOrder[] = debug_backtrace(0, 5)[4]['class']; // access mock class name
        };

        $component1 = $this
            ->getMockBuilder(AbstractComponent::class)
            ->setMockClassName('Component1')
            ->onlyMethods(['boot'])
            ->getMockForAbstractClass();

        $component1
            ->expects(self::once())
            ->method('boot')
            ->willReturnCallback($bootCallback);

        $component2 = $this
            ->getMockBuilder(AbstractComponent::class)
            ->setMockClassName('Component2')
            ->onlyMethods(['boot'])
            ->getMockForAbstractClass();

        $component2
            ->expects(self::once())
            ->method('boot')
            ->willReturnCallback($bootCallback);

        $components = [$component1, $component2];

        $bot = new Bot(new ContainerBuilder(), $components);
        $bot->getContainer();

        self::assertSame(array_map('get_class', $components), $bootOrder);
    }

    /** @test */
    public function shutdown_called_in_reverse_registration_order_at_end_of_run(): void
    {
        $shutdownOrder = [];
        $shutdownCallback = function () use (&$shutdownOrder) {
            $shutdownOrder[] = debug_backtrace(0, 5)[4]['class']; // access mock class name
        };

        $component1 = $this
            ->getMockBuilder(AbstractComponent::class)
            ->setMockClassName('Component3')
            ->onlyMethods(['shutdown'])
            ->getMockForAbstractClass();

        $component1
            ->expects(self::once())
            ->method('shutdown')
            ->willReturnCallback($shutdownCallback);

        $component2 = $this
            ->getMockBuilder(AbstractComponent::class)
            ->setMockClassName('Component4')
            ->onlyMethods(['shutdown'])
            ->getMockForAbstractClass();

        $component2
            ->expects(self::once())
            ->method('shutdown')
            ->willReturnCallback($shutdownCallback);

        $components = [$component1, $component2];

        $bot = new Bot(new ContainerBuilder(), $components);
        $bot->run();

        self::assertSame(array_reverse(array_map('get_class', $components)), $shutdownOrder);
    }
}
