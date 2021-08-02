<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Channel;

final class Channel implements ChannelInterface
{
    private string $id;
    private bool $isPrivate;
    private ?string $name;

    public function __construct(string $id, ?string $name, bool $isPrivate)
    {
        $this->id = $id;
        $this->isPrivate = $isPrivate;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
