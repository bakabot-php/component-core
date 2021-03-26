<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Channel;

use PHPUnit\Framework\TestCase;

class ChannelTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $channel = new Channel(
            '1234567890',
            'some-channel',
            true,
        );

        self::assertSame('1234567890', $channel->getId());
        self::assertSame('some-channel', $channel->getName());
        self::assertTrue($channel->isPrivate());
    }
}
