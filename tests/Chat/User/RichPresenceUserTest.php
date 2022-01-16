<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User;

use Bakabot\Chat\User\Role\Role;
use PHPUnit\Framework\TestCase;

class RichPresenceUserTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $user = new User(
            $id = '1234567890',
            'Nayleen',
            'Minaire',
            false
        );

        $richPresenceUser = new RichPresenceUser($user, $imageUrl = 'some-image-url');

        self::assertSame($id, $richPresenceUser->getId());
        self::assertSame('Nayleen', $richPresenceUser->getUsername());
        self::assertSame('Minaire', $richPresenceUser->getNickname());
        self::assertFalse($richPresenceUser->isBot());
        self::assertSame($imageUrl, $richPresenceUser->getAvatarUrl());
        self::assertSame($id, (string) $richPresenceUser);
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

        $richPresenceUser = new RichPresenceUser($user, '');

        self::assertSame('Nayleen', $richPresenceUser->getNickname());
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

        $richPresenceUser = new RichPresenceUser($user, '');

        $expectedRole = new Role('1234');

        $richPresenceUser->addRole($expectedRole);

        self::assertTrue($richPresenceUser->hasRole('1234'));
        self::assertFalse($richPresenceUser->hasRole('9001'));
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

        $richPresenceUser = new RichPresenceUser($user, '');

        $expectedRole = new Role('1234');

        $richPresenceUser->addRole($expectedRole);

        self::assertTrue($richPresenceUser->hasRole($expectedRole));
    }
}
