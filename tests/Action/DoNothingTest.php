<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Message\MessageInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

class DoNothingTest extends TestCase
{
    /** @test */
    public function does_nothing_but_satisfy_the_action_interface(): void
    {
        $action = new DoNothing();
        $action->setTriggerMessage($this->createMock(MessageInterface::class));

        self::assertFalse($action->deleteTriggerMessage());
        self::assertFalse($action->shouldPingRecipient());

        try {
            $action->getTarget();
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
