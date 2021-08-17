<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Promise;
use Amp\Success;
use Bakabot\Action\DoNothing;
use Bakabot\Action\SendFiles;
use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Command\Payload;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class ProcessorChainTest extends TestCase
{
    /** @test */
    public function noop_when_no_processors_are_added(): void
    {
        $chain = new ProcessorChain();

        $payload = $this->createMock(PayloadInterface::class);
        $action = Promise\wait($chain->process($payload));

        self::assertInstanceOf(DoNothing::class, $action);
    }

    /** @test */
    public function payload_can_be_decorated_between_processors(): void
    {
        $commandPayload = $this->createMock(Payload::class);

        $decorator = $this->createMock(ProcessorInterface::class);
        $decorator->expects($this->once())->method('process')->willReturn(new Success($commandPayload));

        $producer = $this->createMock(ProcessorInterface::class);
        $producer->expects($this->once())->method('process')->with($commandPayload)->willReturn(
            new Success(
                new SendFiles($this->createMock(ChannelInterface::class), [])
            )
        );

        $chain = new ProcessorChain();
        $chain->push($decorator);
        $chain->push($producer);

        $payload = $this->createMock(PayloadInterface::class);
        $action = Promise\wait($chain->process($payload));

        self::assertInstanceOf(SendFiles::class, $action);
    }
}
