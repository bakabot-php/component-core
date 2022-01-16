<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Amp\Loop;
use Amp\Loop\Driver;
use Amp\ReactAdapter\ReactAdapter;
use Bakabot\Chat\Server\Language\FallbackLanguageSource;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Language\LanguageSource;
use Bakabot\Chat\Server\Language\EnvironmentLanguageSource;
use Bakabot\Chat\Server\Settings\SettingsSource;
use Bakabot\Chat\Server\Settings\JsonFileSource;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Prefix\PrefixSource;
use Bakabot\Command\Registry;
use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use Bakabot\Component\Core\Logger\LoggerFactory;
use Bakabot\Kernel;
use Bakabot\Payload\Processor\Firewall\Firewall;
use Bakabot\Payload\Processor\Firewall\Rule\IgnoreBots;
use Bakabot\Payload\Processor\ProcessorChain;
use Bakabot\Payload\Processor\ProcessorFactory;
use Monolog\ErrorHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;
use React\EventLoop\LoopInterface;
use function DI\env;
use function DI\get;
use function DI\string;

#[RegistersParameter('bakabot.debug', 'bool', "e:getenv('APP_DEBUG') ?? false", 'Whether to run the bot in debugging mode')]
#[RegistersParameter('bakabot.default_language', 'string', "e:Locale::getPrimaryLanguage(Locale::getDefault())", 'Default language assumed for servers')]
#[RegistersParameter('bakabot.default_prefix', 'string', '!', 'Default prefix used for commands')]
#[RegistersParameter('bakabot.dirs.base', 'string', '/app', 'Base directory')]
#[RegistersParameter('bakabot.dirs.cache', 'string', '/app/var/cache', 'Cache directory; Used for e.g. compiled DI containers')]
#[RegistersParameter('bakabot.dirs.var', 'string', '/app/var', 'Variable data; The bot *should* not depend on this directory existing')]
#[RegistersParameter('bakabot.env', 'string', "e:getenv('APP_ENV') ?? \"prod\"", 'Name of the environment')]
#[RegistersParameter('bakabot.name', 'string', "e:getenv('APP_NAME') ?? \"Bakabot\"", 'Name used for the bot')]
#[RegistersParameter('bakabot.logs.default.date_format', 'string', 'Y-m-d H:i:s.u', 'Date format used for logs')]
#[RegistersParameter('bakabot.logs.default.level', 'string', "e:getenv('APP_DEBUG') ? LogLevel::DEBUG : LogLevel::INFO", 'Level used for logs')]
#[RegistersParameter('bakabot.logs.default.line_format', 'string', "[%datetime%] [%channel%] %message% %context% %extra%\\n", 'Line format used for logs')]
#[RegistersService('bakabot.logs.default', 'Logs to stdout', LoggerInterface::class)]
#[RegistersService('bakabot.logs.error', 'Logs to stderr', LoggerInterface::class)]
#[RegistersService(Firewall::class)]
#[RegistersService(Logger::class, 'Main application logger', LoggerInterface::class)]
#[RegistersService(LoggerInterface::class, 'Main application logger')]
#[RegistersService(Loop::class, 'Main application loop')]
#[RegistersService(ProcessorChain::class)]
#[RegistersService(Registry::class)]
final class CoreComponent extends AbstractComponent
{
    protected function parameters(): array
    {
        return [
            // core component specific
            'bakabot.debug' => static fn () => (bool) getenv('APP_DEBUG'),
            'bakabot.default_language' => 'en',
            'bakabot.default_prefix' => '!',
            'bakabot.dirs.base' => env('APP_DIR', '/app'),
            'bakabot.dirs.var' => string('{bakabot.dirs.base}/var/{bakabot.env}'),
            'bakabot.dirs.cache' => string('{bakabot.dirs.var}/cache'),
            'bakabot.env' => env('APP_ENV', 'prod'),
            'bakabot.name' => env('APP_NAME', 'Bakabot'),
            'bakabot.name_lc' => static function (ContainerInterface $c) {
                /** @var string $name */
                $name = $c->get('bakabot.name');

                return mb_strtolower($name);
            },

            // logging
            'bakabot.logs.default.date_format' => 'Y-m-d H:i:s.u',
            'bakabot.logs.default.level' => static function (ContainerInterface $c) {
                return $c->get('bakabot.debug')
                    ? LogLevel::DEBUG
                    : LogLevel::INFO;
            },
            'bakabot.logs.default.line_format' => "[%datetime%] [%channel%] %message% %context% %extra%\n",
        ];
    }

    protected function services(): array
    {
        return [
            // core component specific
            Firewall::class => static function (ContainerInterface $c) {
                /** @var LoggerInterface $logger */
                $logger = $c->get(LoggerInterface::class);

                $firewall = new Firewall($logger);
                $firewall->addRule(new IgnoreBots());

                return $firewall;
            },
            Kernel::class => get(Kernel::class),
            LanguageSource::class => static function (ContainerInterface $c) {
                /** @var LanguageSource $main */
                $main = $c->get(EnvironmentLanguageSource::class);
                /** @var string $defaultLanguage */
                $defaultLanguage = $c->get('bakabot.default_language');

                return new FallbackLanguageSource($main, new Language($defaultLanguage));
            },
            PrefixSource::class => static function (ContainerInterface $c) {
                /** @var string $defaultPrefix */
                $defaultPrefix = $c->get('bakabot.default_prefix');

                return new Prefix($defaultPrefix);
            },
            ProcessorChain::class => static function (ContainerInterface $c) {
                /** @var ProcessorFactory $factory */
                $factory = $c->get(ProcessorFactory::class);

                $chain = new ProcessorChain();

                /** @var Firewall $firewall */
                $firewall = $c->get(Firewall::class);
                $chain->push($firewall);
                $chain->push($factory->createCommandParser());
                $chain->push($factory->createCommandRunner());

                return $chain;
            },
            ProcessorFactory::class => static function (ContainerInterface $c) {
                /** @var Registry $registry */
                $registry = $c->get(Registry::class);
                /** @var SettingsSource $serverSettingsSource */
                $serverSettingsSource = $c->get(SettingsSource::class);

                return new ProcessorFactory($registry, $serverSettingsSource);
            },
            Registry::class => static fn() => new Registry(),
            SettingsSource::class => static function (ContainerInterface $c) {
                /** @var string $basePath */
                $basePath = $c->get('bakabot.dirs.var');
                /** @var LanguageSource $languageSource */
                $languageSource = $c->get(LanguageSource::class);
                /** @var PrefixSource $prefixSource */
                $prefixSource = $c->get(PrefixSource::class);

                return new JsonFileSource($basePath, $languageSource, $prefixSource);
            },

            // event loop
            Driver::class => static function (ContainerInterface $c) {
                $driver = (new Loop\DriverFactory())->create();
                /** @var Kernel $kernel */
                $kernel = $c->get(Kernel::class);

                $driver->onSignal(SIGHUP, $kernel->reload($driver));
                $driver->onSignal(SIGINT, $kernel->stop($driver));
                $driver->onSignal(SIGTERM, $kernel->stop($driver));

                return $driver;
            },
            Loop::class => get(Driver::class),
            LoopInterface::class => static fn() => ReactAdapter::get(),

            // logging
            Logger::class => get('bakabot.logs.default'),
            LoggerFactory::class => static function (ContainerInterface $c) {
                /** @var string $defaultLevel */
                $defaultLevel = $c->get('bakabot.logs.default.level');

                return new LoggerFactory($defaultLevel);
            },
            LoggerInterface::class => get(Logger::class),

            'bakabot.logs.default' => static function (ContainerInterface $c) {
                /** @var string $env */
                $env = $c->get('bakabot.env');
                if ($env === 'test') {
                    return new TestLogger();
                }

                /** @var LoggerFactory $loggerFactory */
                $loggerFactory = $c->get(LoggerFactory::class);

                /** @var string $lineFormat */
                $lineFormat = $c->get('bakabot.logs.default.line_format');
                /** @var string $dateFormat */
                $dateFormat = $c->get('bakabot.logs.default.date_format');

                $loggerFactory->addStreamHandler(STDOUT, $lineFormat, $dateFormat);

                /** @var string $lowerCasedName */
                $lowerCasedName = $c->get('bakabot.name_lc');

                return $loggerFactory->create($lowerCasedName);
            },

            'bakabot.logs.error' => static function (ContainerInterface $c) {
                /** @var LoggerFactory $loggerFactory */
                $loggerFactory = $c->get(LoggerFactory::class);

                /** @var string $lineFormat */
                $lineFormat = $c->get('bakabot.logs.default.line_format');
                /** @var string $dateFormat */
                $dateFormat = $c->get('bakabot.logs.default.date_format');

                $loggerFactory->addStreamHandler(STDERR, $lineFormat, $dateFormat, LogLevel::EMERGENCY);

                /** @var string $lowerCasedName */
                $lowerCasedName = $c->get('bakabot.name_lc');

                return $loggerFactory->create($lowerCasedName);
            },
        ];
    }

    public function boot(ContainerInterface $container): void
    {
        // register the global error handling logger
        /** @var LoggerInterface $logger */
        $logger = $container->get('bakabot.logs.error');
        ErrorHandler::register($logger);

        // register the default loop driver
        /** @var Driver $driver */
        $driver = $container->get(Driver::class);
        Loop::set($driver);
    }

    public function shutdown(ContainerInterface $container): void
    {
        Loop::stop();
    }
}
