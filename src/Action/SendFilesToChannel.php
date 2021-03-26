<?php

declare(strict_types = 1);

namespace Bakabot\Action;

use Bakabot\Chat\Channel\ChannelInterface;

final class SendFilesToChannel implements SendFilesInterface
{
    use TriggerMessageTrait;

    private ChannelInterface $channel;
    private array $files;

    public function __construct(ChannelInterface $channel, array $files)
    {
        $this->channel = $channel;
        $this->files = $files;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
