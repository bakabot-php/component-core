<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\MessageInterface;
use PHPUnit\Framework\TestCase;

class DoNothingTest extends TestCase
{
    /** @test */
    public function has_access_to_trigger_message(): void
    {
        $action = new DoNothing();

        self::assertNull($action->getTriggerMessage());
        self::assertFalse($action->shouldDeleteTriggerMessage());
    }

    /** @test */
    public function can_set_and_mark_trigger_message_for_deletion(): void
    {
        $triggerMessage = $this->createMock(MessageInterface::class);

        $action = new DoNothing();
        $action->setTriggerMessage($triggerMessage);
        $action->shouldDeleteTriggerMessage(true);

        self::assertSame($triggerMessage, $action->getTriggerMessage());
        self::assertTrue($action->shouldDeleteTriggerMessage());
    }
}
