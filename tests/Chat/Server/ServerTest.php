<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server;

use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $server = new Server('1234567890', 'my-awesome-server');

        self::assertSame('1234567890', $server->id());
        self::assertSame('my-awesome-server', $server->name());
    }
}
