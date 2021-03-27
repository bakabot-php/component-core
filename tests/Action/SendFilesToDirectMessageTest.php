<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\User\UserInterface;
use PHPUnit\Framework\TestCase;

class SendFilesToDirectMessageTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $recipient = $this->createMock(UserInterface::class);
        $action = new SendFilesToDirectMessage($recipient, []);

        self::assertSame($recipient, $action->getRecipient());
        self::assertSame([], $action->getFiles());
    }
}
