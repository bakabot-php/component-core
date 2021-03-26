<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Channel;

interface ChannelInterface
{
    public function getId(): string;

    public function getName(): ?string;

    public function isPrivate(): bool;
}
