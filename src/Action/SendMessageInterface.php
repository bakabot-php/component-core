<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\User\UserInterface;

interface SendMessageInterface extends ActionInterface
{
    public function getEmbed(): ?array;

    public function getMessage(): ?string;

    public function getRecipient(): ?UserInterface;
}
