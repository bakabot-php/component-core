<?php

declare(strict_types = 1);

namespace Bakabot\Component\Core\Amp\Promise;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action\DoNothing;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class PromisorTest extends TestCase
{
    use Promisor;

    /** @test */
    public function can_promisify_an_action(): void
    {
        $action = new DoNothing();
        $promise = $this->action($action);

        self::assertSame($action, Promise\wait($promise));
    }

    /** @test */
    public function can_defer_an_actions_resolution(): void
    {
        $action = new DoNothing();
        $deferred = new Deferred();

        $promise = $this->action($action, $deferred);

        self::assertTrue($deferred->isResolved());
        self::assertSame($action, Promise\wait($promise));
    }

    /** @test */
    public function can_promisify_a_payload(): void
    {
        $payload = $this->createMock(PayloadInterface::class);
        $promise = $this->message($payload);

        self::assertSame($payload, Promise\wait($promise));
    }

    /** @test */
    public function can_defer_a_payloads_resolution(): void
    {
        $payload = $this->createMock(PayloadInterface::class);
        $deferred = new Deferred();

        $promise = $this->message($payload, $deferred);

        self::assertTrue($deferred->isResolved());
        self::assertSame($payload, Promise\wait($promise));
    }

    /** @test */
    public function promise_promisifies_an_action(): void
    {
        $action = new DoNothing();
        $promise = $this->promise($action);

        self::assertSame($action, Promise\wait($promise));
    }

    /** @test */
    public function promise_promisifies_a_payload(): void
    {
        $payload = $this->createMock(PayloadInterface::class);
        $promise = $this->promise($payload);

        self::assertSame($payload, Promise\wait($promise));
    }
}
