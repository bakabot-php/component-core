<?php

declare(strict_types = 1);

namespace Bakabot;

use Bakabot\Chat\User\UserInterface;
use Bakabot\Payload\PayloadInterface;

interface AdapterInterface
{
    public static function create(UserInterface $activeUser): self;

    public function createPayload(mixed $data): PayloadInterface;

    public function getEnvironment(): EnvironmentInterface;
}
