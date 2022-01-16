<?php

declare(strict_types = 1);

namespace Bakabot\Action;

interface Action
{
    public function mentionRecipient(): Action;

    public function mentionsRecipient(): bool;

    public function removeTriggerMessage(bool $removesTriggerMessage = true): Action;

    public function removesTriggerMessage(): bool;
}
