<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Promise;
use Amp\Success;
use Bakabot\Action\DoNothing;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class LazyProcessorTest extends TestCase
{
    /** @test */
    public function lazily_evaluates_factory(): void
    {
        $result = null;
        $factory = function () use (&$result) {
            $result = 'fail';
        };

        new LazyProcessor($factory);

        self::assertNull($result);
    }

    /** @test */
    public function evaluates_factory_just_in_time_to_process(): void
    {
        $lazilyCreatedProcessor = null;
        $factory = function () use (&$lazilyCreatedProcessor) {
            $lazilyCreatedProcessor = $this->createMock(ProcessorInterface::class);
            $lazilyCreatedProcessor->method('process')->willReturn(new Success(new DoNothing()));

            return $lazilyCreatedProcessor;
        };

        $processor = new LazyProcessor($factory);
        $action = Promise\wait($processor->process($this->createMock(PayloadInterface::class)));

        self::assertInstanceOf(ProcessorInterface::class, $lazilyCreatedProcessor);
        self::assertInstanceOf(DoNothing::class, $action);
    }
}
