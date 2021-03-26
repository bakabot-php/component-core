<?php

declare(strict_types = 1);

namespace Bakabot\Action;

interface SendMessageInterface extends ActionInterface
{
    public function getEmbed(): ?array;

    public function getMessage(): ?string;
}
