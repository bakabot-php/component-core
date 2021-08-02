<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User\Role;

use Bakabot\Chat\IdentifiableInterface;

interface RoleInterface extends IdentifiableInterface
{
    public function getId(): string;
}
