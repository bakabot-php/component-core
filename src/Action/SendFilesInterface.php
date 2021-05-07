<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\User\UserInterface;

interface SendFilesInterface extends ActionInterface
{
    public function getFiles(): array;

    public function getRecipient(): ?UserInterface;
}
