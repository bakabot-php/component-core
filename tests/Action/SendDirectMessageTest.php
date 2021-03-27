<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\User\UserInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

class SendDirectMessageTest extends TestCase
{
    /** @test */
    public function needs_either_message_or_embed(): void
    {
        $this->expectException(LogicException::class);

        new SendDirectMessage($this->createMock(UserInterface::class), null, null);
    }

    /** @test */
    public function acts_as_dto(): void
    {
        $recipient = $this->createMock(UserInterface::class);
        $action = new SendDirectMessage($recipient, 'Hello World!', []);

        self::assertSame($recipient, $action->getRecipient());
        self::assertSame('Hello World!', $action->getMessage());
        self::assertSame([], $action->getEmbed());
    }

    /** @test */
    public function static_constructors(): void
    {
        $recipient = $this->createMock(UserInterface::class);
        $action = SendDirectMessage::withMessage($recipient, 'Hello World!');

        self::assertSame('Hello World!', $action->getMessage());
        self::assertNull($action->getEmbed());

        $action = SendDirectMessage::withEmbed($recipient, []);

        self::assertNull($action->getMessage());
        self::assertSame([], $action->getEmbed());
    }
}
