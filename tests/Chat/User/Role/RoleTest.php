<?php

declare(strict_types = 1);

namespace Bakabot\Chat\User\Role;

use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $role = new Role('1234567890');

        self::assertSame('1234567890', $role->getId());
    }
}
