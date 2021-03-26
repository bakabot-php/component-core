<?php

declare(strict_types = 1);

namespace Bakabot\Payload;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\Server\ServerInterface;
use Bakabot\EnvironmentInterface;

interface PayloadInterface
{
    public function getAllowedCommands(): ?array;

    public function getChannel(): ChannelInterface;

    public function getEnvironment(): EnvironmentInterface;

    public function getMessage(): MessageInterface;

    public function getServer(): ?ServerInterface;
}
