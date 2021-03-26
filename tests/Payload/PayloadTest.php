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
        self::assertNull($payload->getAllowedCommands());
    }

    /** @test */
    public function can_be_decorated_with_allowed_commands(): void
    {
        $payload = new Payload(
            $environment = $this->createMock(EnvironmentInterface::class),
            $channel = $this->createMock(ChannelInterface::class),
            $message = $this->createMock(MessageInterface::class),
            $server = $this->createMock(ServerInterface::class),
            null
        );

        $decoratedPayload = Payload::withAllowedCommands($payload, ['my_command']);

        self::assertSame($environment, $decoratedPayload->getEnvironment());
        self::assertSame($channel, $decoratedPayload->getChannel());
        self::assertSame($message, $decoratedPayload->getMessage());
        self::assertSame($server, $decoratedPayload->getServer());
        self::assertSame(['my_command'], $decoratedPayload->getAllowedCommands());
    }
}
