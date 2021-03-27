<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\User\UserInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

class SendMessageTest extends TestCase
{
    /** @test */
    public function needs_either_message_or_embed(): void
    {
        $this->expectException(LogicException::class);

        new SendMessage($this->createMock(ChannelInterface::class), null, null);
    }

    /** @test */
    public function acts_as_dto(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $action = new SendMessage($channel, 'Hello World!', []);

        self::assertSame($channel, $action->getChannel());
        self::assertNull($action->getRecipient());
        self::assertSame('Hello World!', $action->getMessage());
        self::assertSame([], $action->getEmbed());
        self::assertFalse($action->shouldPingRecipient());
    }

    /** @test */
    public function static_constructors(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $action = SendMessage::withMessage($channel, 'Hello World!');

        self::assertSame('Hello World!', $action->getMessage());
        self::assertNull($action->getEmbed());

        $action = SendMessage::withEmbed($channel, []);

        self::assertNull($action->getMessage());
        self::assertSame([], $action->getEmbed());
    }

    /** @test */
    public function should_not_ping_in_private_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isPrivate')->willReturn(true);

        $recipient = $this->createMock(UserInterface::class);

        $action = new SendMessage($channel, 'Hello World!', [], $recipient, true);

        self::assertFalse($action->shouldPingRecipient());
    }

    /** @test */
    public function should_not_ping_if_forbidden(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isPrivate')->willReturn(false);

        $recipient = $this->createMock(UserInterface::class);

        $action = new SendMessage($channel, 'Hello World!', [], $recipient, false);

        self::assertFalse($action->shouldPingRecipient());
    }

    /** @test */
    public function should_ping_if_set(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isPrivate')->willReturn(false);

        $recipient = $this->createMock(UserInterface::class);

        $action = new SendMessage($channel, 'Hello World!', [], $recipient, true);

        self::assertTrue($action->shouldPingRecipient());
    }
}
