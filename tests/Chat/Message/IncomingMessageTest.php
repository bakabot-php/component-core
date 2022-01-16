<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message;

use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\User\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class IncomingMessageTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $message = new Message(
            $id = '1234567890',
            'Lorem Ipsum',
            $channel = $this->createMock(Channel::class),
            $author = $this->createMock(User::class),
            $creationTime = new DateTime(),
            null
        );

        self::assertSame('1234567890', $message->id());
        self::assertSame('Lorem Ipsum', $message->getContent());
        self::assertSame($channel, $message->getChannel());
        self::assertSame($author, $message->getAuthor());
        self::assertSame($creationTime, $message->getCreationTime());
        self::assertNull($message->getEditedTime());
        self::assertFalse($message->wasModified());
        self::assertSame($id, (string) $message);
    }

    /** @test */
    public function message_with_edited_time_is_considered_edited(): void
    {
        $message = new Message(
            '1234567890',
            'Lorem Ipsum',
            $this->createMock(Channel::class),
            $this->createMock(User::class),
            new DateTime(),
            $editedTime = new DateTime()
        );

        self::assertSame($editedTime, $message->getEditedTime());
        self::assertTrue($message->wasModified());
    }
}
