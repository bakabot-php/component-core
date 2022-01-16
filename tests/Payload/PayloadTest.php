<?php

declare(strict_types = 1);

namespace Bakabot\Payload;

use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Server\Server;
use Bakabot\Environment;
use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $payload = new BasePayload(
            $environment = $this->createMock(Environment::class),
            $channel = $this->createMock(Channel::class),
            $message = $this->createMock(Message::class),
            $server = $this->createMock(Server::class),
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
        $payload = new BasePayload(
            $environment = $this->createMock(Environment::class),
            $channel = $this->createMock(Channel::class),
            $message = $this->createMock(Message::class),
            $server = $this->createMock(Server::class),
        );

        self::assertSame($environment, $payload->getEnvironment());
        self::assertSame($channel, $payload->getChannel());
        self::assertSame($message, $payload->getMessage());
        self::assertSame($server, $payload->getServer());
    }
}
