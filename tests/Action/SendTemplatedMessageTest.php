<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use PHPUnit\Framework\TestCase;

class SendTemplatedMessageTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $message = 'Hello {{ name }}!';
        $context = [
            'name' => 'World',
        ];

        $action = new SendTemplatedMessage(
            $this->createMock(ChannelInterface::class),
            $message,
            $context
        );

        self::assertSame($context, $action->getContext());
        self::assertSame($message, $action->getMessage());
    }
}
