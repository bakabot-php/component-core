<?php

declare(strict_types = 1);

namespace Bakabot\Payload;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\Server\ServerInterface;
use Bakabot\EnvironmentInterface;

final class Payload implements PayloadInterface
{
    private ChannelInterface $channel;
    private EnvironmentInterface $environment;
    private MessageInterface $message;
    private ?ServerInterface $server;

    /**
     * @param EnvironmentInterface $environment
     * @param ChannelInterface $channel
     * @param MessageInterface $message
     * @param ServerInterface|null $server
     */
    public function __construct(
        EnvironmentInterface $environment,
        ChannelInterface $channel,
        MessageInterface $message,
        ?ServerInterface $server
    ) {
        $this->channel = $channel;
        $this->environment = $environment;
        $this->message = $message;
        $this->server = $server;
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
