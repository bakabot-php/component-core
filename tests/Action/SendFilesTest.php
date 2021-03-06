<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use PHPUnit\Framework\TestCase;

class SendFilesTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $files = ['https://i.imgur.com/ntGwBPm.jpeg'];
        $action = new SendFiles($this->createMock(ChannelInterface::class), $files);

        self::assertSame($files, $action->getFiles());
    }
}
