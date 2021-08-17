<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\Server\ServerInterface;
use Bakabot\EnvironmentInterface;
use Bakabot\Payload\PayloadInterface;

final class Payload implements PayloadInterface
{
    private string $commandName;
    /** @var string[] */
    private array $parsedArguments;
    private string $prefix;
    private string $rawArguments;
    private PayloadInterface $wrappedPayload;

    /**
     * @param PayloadInterface $wrappedPayload
     * @param string $prefix
     * @param string $commandName
     * @param string[] $parsedArguments
     * @param string $rawArguments
     */
    public function __construct(
        PayloadInterface $wrappedPayload,
        string $prefix,
        string $commandName,
        array $parsedArguments,
        string $rawArguments
    ) {
        $this->commandName = $commandName;
        $this->parsedArguments = $parsedArguments;
        $this->prefix = $prefix;
        $this->rawArguments = $rawArguments;
        $this->wrappedPayload = $wrappedPayload;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->wrappedPayload->getChannel();
    }

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function getCommandPrefix(): string
    {
        return $this->prefix;
    }

    public function getEnvironment(): EnvironmentInterface
    {
        return $this->wrappedPayload->getEnvironment();
    }

    public function getMessage(): MessageInterface
    {
        return $this->wrappedPayload->getMessage();
    }

    /** @return string[] */
    public function getParsedArguments(): array
    {
        return $this->parsedArguments;
    }

    public function getRawArguments(): string
    {
        return $this->rawArguments;
    }

    public function getServer(): ?ServerInterface
    {
        return $this->wrappedPayload->getServer();
    }
}
