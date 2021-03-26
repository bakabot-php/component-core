<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\User\UserInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

class IncomingMessageTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $message = new IncomingMessage(
            '1234567890',
            'Lorem Ipsum',
            $channel = $this->createMock(ChannelInterface::class),
            $author = $this->createMock(UserInterface::class),
            $creationTime = new DateTime(),
            null
        );

        self::assertSame('1234567890', $message->getId());
        self::assertSame('Lorem Ipsum', $message->getContent());
        self::assertSame($channel, $message->getChannel());
        self::assertSame($author, $message->getAuthor());
        self::assertSame($creationTime, $message->getCreationTime());
        self::assertNull($message->getEditedTime());
        self::assertFalse($message->wasEdited());
    }

    /** @test */
    public function message_with_edited_time_is_considered_edited(): void
    {
        $message = new IncomingMessage(
            '1234567890',
            'Lorem Ipsum',
            $this->createMock(ChannelInterface::class),
            $this->createMock(UserInterface::class),
            new DateTime(),
            $editedTime = new DateTime()
        );

        self::assertSame($editedTime, $message->getEditedTime());
        self::assertTrue($message->wasEdited());
    }
}
