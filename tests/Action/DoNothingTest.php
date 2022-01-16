<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\Message;
use LogicException;
use PHPUnit\Framework\TestCase;

class DoNothingTest extends TestCase
{
    /** @test */
    public function does_nothing_but_satisfy_the_action_interface(): void
    {
        $action = new DoNothing();
        $action->setTriggerMessage($this->createMock(Message::class));

        self::assertFalse($action->deleteTriggerMessage());
        self::assertFalse($action->mentionsRecipient());

        try {
            $action->target();
        } catch (LogicException) {
            $this->addToAssertionCount(1);
        }

        try {
            $action->getTriggerMessage();
        } catch (LogicException) {
            $this->addToAssertionCount(1);
        }
    }
}
