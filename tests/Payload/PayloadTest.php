<?php

declare(strict_types = 1);

namespace Bakabot\Payload;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\Server\ServerInterface;
use Bakabot\EnvironmentInterface;
use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $payload = new Payload(
            $environment = $this->createMock(EnvironmentInterface::class),
            $channel = $this->createMock(ChannelInterface::class),
            $message = $this->createMock(MessageInterface::class),
            $server = $this->createMock(ServerInterface::class),
            null
        );

        self::assertSame($environment, $payload->getEnvironment());
        self::assertSame($channel, $payload->getChannel());
        self::assertSame($message, $payload->getMessage());
        self::assertSame($server, $payload->getServer());
    }

    /** @test */
    public function can_be_decorated_with_allowed_commands(): void
    {
        $payload = new Payload(
            $environment = $this->createMock(EnvironmentInterface::class),
            $channel = $this->createMock(ChannelInterface::class),
            $message = $this->createMock(MessageInterface::class),
            $server = $this->createMock(ServerInterface::class),
        );

        self::assertSame($environment, $payload->getEnvironment());
        self::assertSame($channel, $payload->getChannel());
        self::assertSame($message, $payload->getMessage());
        self::assertSame($server, $payload->getServer());
    }
}
