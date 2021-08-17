<?php

declare(strict_types = 1);

namespace Bakabot\Component\Core\Logger;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Log\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    private string $defaultLevel;
    /** @var HandlerInterface[] */
    private array $handlers = [];

    public function __construct(string $defaultLevel)
    {
        $this->defaultLevel = $defaultLevel;
    }

    public function create(?string $name = null): LoggerInterface
    {
        $logger = new Logger($name ?? bin2hex(random_bytes(3)));

        foreach ($this->handlers as $handler) {
            $logger->pushHandler($handler);
        }

        $this->handlers = [];

        return $logger;
    }

    /** @param resource $stream */
    public function addStreamHandler(
        mixed $stream,
        string $lineFormat,
        string $dateFormat,
        string $level = null,
        bool $includeStacktraces = true
    ): self {
        $formatter = new LineFormatter($lineFormat, $dateFormat, false, true);
        $formatter->includeStacktraces($includeStacktraces);

        $handler = new StreamHandler(new ResourceOutputStream($stream), $level ?? $this->defaultLevel);
        $handler->setFormatter($formatter);

        $this->handlers[] = $handler;

        return $this;
    }
}
