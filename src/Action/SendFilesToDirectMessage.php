<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\User\UserInterface;

final class SendFilesToDirectMessage implements SendFilesInterface
{
    use TriggerMessageTrait;

    private array $files;
    private UserInterface $recipient;

    public function __construct(UserInterface $recipient, array $files)
    {
        $this->files = $files;
        $this->recipient = $recipient;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getRecipient(): UserInterface
    {
        return $this->recipient;
    }
}
