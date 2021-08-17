<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;

class CoreComponentTest extends ComponentTestCase
{
    protected function getComponent(): Component
    {
        return new CoreComponent();
    }

    /** @test */
    public function registers_log_level_depending_on_app_debug(): void
    {
        putenv('APP_DEBUG=true');
        $container = $this->getContainer();

        self::assertTrue($container->has('bakabot.logs.default.level'));
        self::assertSame(LogLevel::DEBUG, $container->get('bakabot.logs.default.level'));

        putenv('APP_DEBUG=');
        $container = $this->getContainer();

        self::assertTrue($container->has('bakabot.logs.default.level'));
        self::assertSame(LogLevel::INFO, $container->get('bakabot.logs.default.level'));
    }

    /** @test */
    public function registers_monolog_normally(): void
    {
        putenv('APP_ENV=prod');
        $container = $this->getContainer();

        self::assertInstanceOf(Logger::class, $container->get(LoggerInterface::class));
    }

    /** @test */
    public function registers_test_logger_in_testing_environment(): void
    {
        putenv('APP_ENV=test');
        $container = $this->getContainer();

        self::assertInstanceOf(TestLogger::class, $container->get(LoggerInterface::class));
    }
}
