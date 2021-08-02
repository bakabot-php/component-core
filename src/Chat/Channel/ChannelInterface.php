<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Channel;

use Bakabot\Chat\TargetInterface;

interface ChannelInterface extends TargetInterface
{
    public function getId(): string;

    public function getName(): ?string;

    public function isPrivate(): bool;
}
