<?php

declare(strict_types = 1);

namespace Bakabot\Chat;

interface RichPresenceInterface
{
    public function getDisplayImageUrl(): string;
}
