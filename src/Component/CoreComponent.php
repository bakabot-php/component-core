<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Amp\Loop;
use Amp\Loop\Driver;
use Amp\ReactAdapter\ReactAdapter;
use Bakabot\Command\Registry;
use Bakabot\Component\Core\Amp\Loop\RebootException;
use Bakabot\Component\Core\Logger\LoggerFactory;
use Monolog\ErrorHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use React\EventLoop\LoopInterface;

use function DI\env;
use function DI\get;
use function DI\string;

final class CoreComponent extends AbstractComponent implements DependentComponentInterface
{
    public function boot(ContainerInterface $container): void
    {
        // register the global error handling logger
        ErrorHandler::register($container->get('bakabot.logs.error'));

        // register the default loop driver
        Loop::set($container->get(Driver::class));
    }

    public function shutdown(ContainerInterface $container): void
    {
        Loop::stop();
    }

    protected function getParameters(): array
    {
        return [
            // globals
            'bakabot.debug' => env('APP_DEBUG', false),
            'bakabot.default_prefix' => '!',
            'bakabot.dirs.base' => env('APP_DIR', '/app'),
            'bakabot.dirs.cache' => string('{bakabot.dirs.var}/cache'),
            'bakabot.dirs.logs' => string('{bakabot.dirs.var}/log'),
            'bakabot.dirs.var' => string('{bakabot.dirs.base}/var'),
            'bakabot.env' => env('APP_ENV', 'prod'),
            'bakabot.name' => env('APP_NAME', 'Bakabot'),

            // core component specific

            // database component specific
            'bakabot.db.config' => [
                'url' => env('DB_CONNECTION_DEFAULT_URL', string('sqlite://{bakabot.dirs.var}/database.sqlite')),
            ],

            // logging
            'bakabot.logs.default.level' => static function (ContainerInterface $c) {
                return $c->get('bakabot.debug')
                    ? LogLevel::DEBUG
                    : LogLevel::INFO;
            },
            'bakabot.logs.default.max_files' => 5,

            'bakabot.logs.file.date_format' => 'Y-m-d H:i:s.u',
            'bakabot.logs.file.level' => get('bakabot.logs.default.level'),
            'bakabot.logs.file.line_format' => static function (ContainerInterface $c) {
                return $c->get('bakabot.debug')
                    ? "[%datetime%] [%channel%] %message% %context% %extra%\n"
                    : "%message% %context% %extra%\n";
            },

            'bakabot.logs.stdout.date_format' => 'H:i:s.u',
            'bakabot.logs.stdout.level' => get('bakabot.logs.default.level'),
            'bakabot.logs.stdout.line_format' => "[%datetime%] %message% %context% %extra%\n",
        ];
    }

    protected function getServices(): array
    {
        return [
            // core component specific
            Registry::class => static fn() => new Registry(),

            // event loop
            Driver::class => static function (ContainerInterface $c) {
                $driver = (new Loop\DriverFactory())->create();

                $stopHandler = static function () use ($driver): void {
                    $driver->stop();
                };

                $driver->onSignal(SIGHUP, static function () use ($driver): void {
                    $driver->stop();
                    throw new RebootException();
                });
                $driver->onSignal(SIGINT, $stopHandler);
                $driver->onSignal(SIGTERM, $stopHandler);

                return $driver;
            },
            Loop::class => get(Driver::class),
            LoopInterface::class => static fn() => ReactAdapter::get(),

            // logging
            Logger::class => get('bakabot.logs.stdout'),
            LoggerFactory::class => static function (ContainerInterface $c) {
                return new LoggerFactory(
                    $c->get('bakabot.dirs.logs'),
                    $c->get('bakabot.logs.default.level'),
                    $c->get('bakabot.logs.default.max_files')
                );
            },
            LoggerInterface::class => get(Logger::class),

            'bakabot.logs.error' => static function (ContainerInterface $c) {
                /** @var LoggerFactory $loggerFactory */
                $loggerFactory = $c->get(LoggerFactory::class);
                $loggerFactory->addFileHandler(
                    'error.log',
                    $c->get('bakabot.logs.file.line_format'),
                    $c->get('bakabot.logs.file.date_format'),
                    LogLevel::ERROR,
                    0
                );

                if ($c->get('bakabot.debug')) {
                    $loggerFactory->addStreamHandler(
                        STDERR,
                        $c->get('bakabot.logs.stdout.line_format'),
                        $c->get('bakabot.logs.stdout.date_format'),
                        LogLevel::ERROR
                    );
                }

                return $loggerFactory->create($c->get('bakabot.name'));
            },

            'bakabot.logs.stdout' => static function (ContainerInterface $c) {
                /** @var LoggerFactory $loggerFactory */
                $loggerFactory = $c->get(LoggerFactory::class);
                $loggerFactory->addStreamHandler(
                    STDOUT,
                    $c->get('bakabot.logs.stdout.line_format'),
                    $c->get('bakabot.logs.stdout.date_format'),
                    $c->get('bakabot.logs.stdout.level')
                );

                return $loggerFactory->create($c->get('bakabot.name'));
            },
        ];
    }

    public function getComponentDependencies(): array
    {
        return [
            DatabaseComponent::class,
        ];
    }
}
