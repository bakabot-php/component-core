<?php

declare(strict_types = 1);

namespace Bakabot\Payload;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\Server\ServerInterface;
use Bakabot\EnvironmentInterface;

final class Payload implements PayloadInterface
{
    private ?array $allowedCommands;
    private ChannelInterface $channel;
    private EnvironmentInterface $environment;
    private MessageInterface $message;
    private ?ServerInterface $server;

    public function __construct(
        EnvironmentInterface $environment,
        ChannelInterface $channel,
        MessageInterface $message,
        ?ServerInterface $server,
        ?array $allowedCommands = null
    ) {
        $this->allowedCommands = $allowedCommands;
        $this->channel = $channel;
        $this->environment = $environment;
        $this->message = $message;
        $this->server = $server;
    }

    public static function withAllowedCommands(PayloadInterface $payload, ?array $allowedCommands): Payload
    {
        return new Payload(
            $payload->getEnvironment(),
            $payload->getChannel(),
            $payload->getMessage(),
            $payload->getServer(),
            $allowedCommands
        );
    }

    public function getAllowedCommands(): ?array
    {
        return $this->allowedCommands;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getEnvironment(): EnvironmentInterface
    {
        return $this->environment;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    public function getServer(): ?ServerInterface
    {
        return $this->server;
    }
}
