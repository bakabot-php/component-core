# bakabot-core [![Latest Stable Version](https://poser.pugx.org/bakabot/component-core/v)](//packagist.org/packages/bakabot/component-core) [![License](https://poser.pugx.org/bakabot/component-core/license)](//packagist.org/packages/bakabot/component-core) [![Build Status](https://travis-ci.com/bakabot-php/component-core.svg?branch=main)](https://travis-ci.com/bakabot-php/component-core)
Provides the core functionality and facilities of the bot.

## Installation
`composer require bakabot/component-core`

## Configuration

### Parameters
| Name                               | Type     | Default Value                                              | Description                                                           |
|------------------------------------|----------|------------------------------------------------------------|-----------------------------------------------------------------------|
| `bakabot.debug`                    | `bool`   | `getenv('APP_DEBUG') ?? false`                             | Whether to run the bot in debugging mode                              |
| `bakabot.default_language`         | `string` | `Locale::getPrimaryLanguage(Locale::getDefault())`         | Default language assumed for servers                                  |
| `bakabot.default_prefix`           | `string` | `"!"`                                                      | Default prefix used for commands                                      |
| `bakabot.dirs.base`                | `string` | `"/app"`                                                   | Base directory                                                        |
| `bakabot.dirs.cache`               | `string` | `"/app/var/cache"`                                         | Cache directory; Used for e.g. compiled DI containers                 |
| `bakabot.dirs.var`                 | `string` | `"/app/var"`                                               | Variable data; The bot *should* not depend on this directory existing |
| `bakabot.env`                      | `string` | `getenv('APP_ENV') ?? "prod"`                              | Name of the environment                                               |
| `bakabot.logs.default.date_format` | `string` | `"Y-m-d H:i:s.u"`                                          | Date format used for logs                                             |
| `bakabot.logs.default.level`       | `string` | `getenv('APP_DEBUG') ? LogLevel::DEBUG : LogLevel::INFO`   | Level used for logs                                                   |
| `bakabot.logs.default.line_format` | `string` | `"[%datetime%] [%channel%] %message% %context% %extra%\n"` | Line format used for logs                                             |
| `bakabot.name`                     | `string` | `getenv('APP_NAME') ?? "Bakabot"`                          | Name used for the bot                                                 |


### Services
| Name                                                   | Description               |
|--------------------------------------------------------|---------------------------|
| `Bakabot\Command\Registry`                             | No description available. |
| `Bakabot\Payload\Processor\Firewall\Firewall`          | No description available. |
| `Bakabot\Payload\Processor\ProcessorChain`             | No description available. |
| `Monolog\Logger` (provides: `Psr\Log\LoggerInterface`) | Main application logger   |
| `Psr\Log\LoggerInterface`                              | Main application logger   |
| "bakabot.logs.default" (is: `Psr\Log\LoggerInterface`) | Logs to stdout            |
| "bakabot.logs.error" (is: `Psr\Log\LoggerInterface`)   | Logs to stderr            |

