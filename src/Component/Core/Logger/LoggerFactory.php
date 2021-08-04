<?php

declare(strict_types = 1);

namespace Bakabot\Component\Core\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    private string $basePath;
    private string $defaultLevel;
    private int $maxFiles;
    /** @var HandlerInterface[] */
    private array $handlers = [];

    public function __construct(
        string $basePath,
        string $defaultLevel,
        int $maxFiles
    ) {
        $this->basePath = $basePath;
        $this->defaultLevel = $defaultLevel;
        $this->maxFiles = $maxFiles;
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

    /** @param string|resource $stream */
    public function addStreamHandler(
        mixed $stream,
        string $lineFormat,
        string $dateFormat,
        string $level = null,
        bool $includeStacktraces = true
    ): self {
        $formatter = new LineFormatter($lineFormat, $dateFormat, false, true);
        $formatter->includeStacktraces($includeStacktraces);

        $handler = new StreamHandler($stream, $level ?? $this->defaultLevel);
        $handler->setFormatter($formatter);

        $this->handlers[] = $handler;

        return $this;
    }

    public function addFileHandler(
        string $filename,
        string $lineFormat,
        string $dateFormat,
        ?string $level = null,
        ?int $maxFiles = null
    ): self {
        $handler = new RotatingFileHandler(
            sprintf('%s/%s', $this->basePath, $filename),
            $maxFiles ?? $this->maxFiles,
            $level ?? $this->defaultLevel,
            true,
            0755
        );
        $handler->setFormatter(new LineFormatter($lineFormat, $dateFormat, false, true));

        $this->handlers[] = $handler;

        return $this;
    }
}
