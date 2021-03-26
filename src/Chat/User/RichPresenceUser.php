<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use Bakabot\Chat\RichPresenceInterface;
use Bakabot\Chat\User\Role\RoleInterface;

final class RichPresenceUser implements RichPresenceInterface, UserInterface
{
    private string $displayImageUrl;
    private UserInterface $user;

    public function __construct(UserInterface $user, string $displayImageUrl)
    {
        $this->displayImageUrl = $displayImageUrl;
        $this->user = $user;
    }

    public function addRole(RoleInterface $role): void
    {
        $this->user->addRole($role);
    }

    public function getDisplayImageUrl(): string
    {
        return $this->displayImageUrl;
    }

    public function getId(): string
    {
        return $this->user->getId();
    }

    public function getNickname(): string
    {
        return $this->user->getNickname();
    }

    public function getUsername(): string
    {
        return $this->user->getUsername();
    }

    public function hasRole(string|RoleInterface $role): bool
    {
        return $this->user->hasRole($role);
    }

    public function isBot(): bool
    {
        return $this->user->isBot();
    }
}
