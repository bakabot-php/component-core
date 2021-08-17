<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\User\UserInterface;
use PHPUnit\Framework\TestCase;

class SendMessageTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $action = new SendMessage($channel, 'Hello World!');

        self::assertSame($channel, $action->getTarget());
        self::assertSame('Hello World!', $action->getMessage());
        self::assertSame([], $action->getContext());
    }

    /** @test */
    public function should_ping_in_public_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isPrivate')->willReturn(false);

        $action = new SendMessage($channel, 'Hello World!');

        self::assertTrue($action->shouldPingRecipient());
    }

    /** @test */
    public function should_not_ping_in_private_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isPrivate')->willReturn(true);

        $action = new SendMessage($channel, 'Hello World!');

        self::assertFalse($action->shouldPingRecipient());
    }

    /** @test */
    public function should_not_ping_if_message_came_from_private_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isPrivate')->willReturn(true);

        $message = $this->createMock(MessageInterface::class);
        $message->expects(self::once())->method('getChannel')->willReturn($channel);

        $action = new SendMessage($message, 'Hello World!');

        self::assertFalse($action->shouldPingRecipient());
    }

    /** @test */
    public function should_ping_if_message_came_from_public_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('isPrivate')->willReturn(false);

        $message = $this->createMock(MessageInterface::class);
        $message->expects(self::once())->method('getChannel')->willReturn($channel);

        $action = new SendMessage($message, 'Hello World!');

        self::assertTrue($action->shouldPingRecipient());
    }

    /** @test */
    public function should_not_ping_if_responding_to_user(): void
    {
        $user = $this->createMock(UserInterface::class);
        $action = new SendMessage($user, 'Hello World!');

        self::assertFalse($action->shouldPingRecipient());
    }

    /** @test */
    public function should_not_ping_if_forbidden(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $action = new SendMessage($channel, 'Hello World!', [], false);

        self::assertFalse($action->shouldPingRecipient());
    }

    /** @test */
    public function should_ping_if_set(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $action = new SendMessage($channel, 'Hello World!', [], true);

        self::assertTrue($action->shouldPingRecipient());
    }
}
