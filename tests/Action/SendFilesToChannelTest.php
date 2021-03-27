<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use PHPUnit\Framework\TestCase;

class SendFilesToChannelTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $action = new SendFilesToChannel($channel, []);

        self::assertSame($channel, $action->getChannel());
        self::assertSame([], $action->getFiles());
    }
}
