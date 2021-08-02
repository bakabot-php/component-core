<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use Bakabot\Chat\User\Role\RoleInterface;

final class User implements UserInterface
{
    private string $id;
    private bool $isBot;
    private ?string $nickname;
    /** @var RoleInterface[] */
    private array $roles = [];
    private string $username;

    public function __construct(string $id, string $username, ?string $nickname = null, bool $isBot = false)
    {
        $this->id = $id;
        $this->isBot = $isBot;
        $this->nickname = $nickname;
        $this->username = $username;
    }

    private function identify(string|RoleInterface $role): string
    {
        if (is_string($role)) {
            return $role;
        }

        return $role->getId();
    }

    public function addRole(RoleInterface $role): void
    {
        $this->roles[$role->getId()] = $role;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNickname(): string
    {
        return $this->nickname ?? $this->username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function hasRole(string|RoleInterface $role): bool
    {
        $roleId = $this->identify($role);

        return isset($this->roles[$roleId]);
    }

    public function isBot(): bool
    {
        return $this->isBot;
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
