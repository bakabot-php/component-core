<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use Bakabot\Chat\User\Role\RoleInterface;

interface UserInterface
{
    public function addRole(RoleInterface $role): void;

    public function getId(): string;

    public function getNickname(): string;

    public function getUsername(): string;

    public function hasRole(string|RoleInterface $role): bool;

    public function isBot(): bool;
}
