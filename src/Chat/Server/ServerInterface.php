<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server;

interface ServerInterface
{
    public function getId(): string;

    public function getName(): ?string;
}
