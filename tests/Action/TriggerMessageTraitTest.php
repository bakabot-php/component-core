<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Message\Message;
use PHPUnit\Framework\TestCase;

class TriggerMessageTraitTest extends TestCase
{
    /** @test */
    public function can_fetch_previously_set_trigger_message(): void
    {
        $message = $this->createMock(Message::class);

        $action = new SendRawMessage($this->createMock(Channel::class), 'Hello World!');
        $action->setTriggerMessage($message);

        self::assertSame($message, $action->getTriggerMessage());
    }

    /** @test */
    public function can_be_flagged_for_trigger_message_deletion(): void
    {
        $action = new SendRawMessage($this->createMock(Channel::class), 'Hello World!');

        self::assertFalse($action->deleteTriggerMessage());
        self::assertTrue($action->deleteTriggerMessage(true));
        self::assertTrue($action->deleteTriggerMessage());
    }
}
