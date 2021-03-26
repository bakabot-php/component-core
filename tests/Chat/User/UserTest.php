<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use Bakabot\Chat\User\Role\Role;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $user = new User(
            '1234567890',
            'Nayleen',
            'Minaire',
            false
        );

        self::assertSame('1234567890', $user->getId());
        self::assertSame('Nayleen', $user->getUsername());
        self::assertSame('Minaire', $user->getNickname());
        self::assertFalse($user->isBot());
    }

    /** @test */
    public function falls_back_to_username_when_nickname_is_missing(): void
    {
        $user = new User(
            '1234567890',
            'Nayleen',
            null,
            false
        );

        self::assertSame('Nayleen', $user->getNickname());
    }

    /** @test */
    public function returns_registered_roles(): void
    {
        $user = new User(
            '1234567890',
            'Nayleen',
            null,
            false
        );

        $expectedRole = new Role('1234');

        $user->addRole($expectedRole);

        self::assertTrue($user->hasRole('1234'));
        self::assertFalse($user->hasRole('9001'));
    }

    /** @test */
    public function allows_role_lookup_by_instance(): void
    {
        $user = new User(
            '1234567890',
            'Nayleen',
            null,
            false
        );

        $expectedRole = new Role('1234');

        $user->addRole($expectedRole);

        self::assertTrue($user->hasRole($expectedRole));
    }
}
