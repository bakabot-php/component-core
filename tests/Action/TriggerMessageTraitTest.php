<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use PHPUnit\Framework\TestCase;

class TriggerMessageTraitTest extends TestCase
{
    /** @test */
    public function can_fetch_previously_set_trigger_message(): void
    {
        $message = $this->createMock(MessageInterface::class);

        $action = new SendMessage($this->createMock(ChannelInterface::class), 'Hello World!');
        $action->setTriggerMessage($message);

        self::assertSame($message, $action->getTriggerMessage());
    }

    /** @test */
    public function can_be_flagged_for_trigger_message_deletion(): void
    {
        $action = new SendMessage($this->createMock(ChannelInterface::class), 'Hello World!');

        self::assertFalse($action->deleteTriggerMessage());
        self::assertTrue($action->deleteTriggerMessage(true));
        self::assertTrue($action->deleteTriggerMessage());
    }
}
