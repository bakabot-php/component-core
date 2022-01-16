<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall;

use Amp\Failure;
use Amp\Promise;
use Bakabot\Action\DoNothing;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\Rule\IgnoreBots;
use Bakabot\Payload\Processor\Firewall\Rule\Rule;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FirewallTest extends TestCase
{
    /** @test */
    public function noop_when_no_rules_are_added(): void
    {
        $firewall = new Firewall(new NullLogger());

        $payload = $this->createMock(PayloadInterface::class);
        $returnedPayload = Promise\wait($firewall->process($payload));

        self::assertSame($payload, $returnedPayload);
    }

    /** @test */
    public function returns_original_payload_if_no_rule_is_violated(): void
    {
        $firewall = new Firewall(new NullLogger());
        $firewall->addRule(new IgnoreBots());

        $payload = $this->createMock(PayloadInterface::class);
        $returnedPayload = Promise\wait($firewall->process($payload));

        self::assertSame($payload, $returnedPayload);
    }

    /** @test */
    public function returns_do_nothing_action_if_rule_is_violated(): void
    {
        $payload = $this->createMock(PayloadInterface::class);
        $fail = $this->createMock(Rule::class);

        $fail->method('enforce')->willReturn(new Failure(new RuleViolation($fail)));
        $fail->method('getName')->willReturn('test:dummy');

        $firewall = new Firewall(new NullLogger());
        $firewall->addRule($fail);

        $action = Promise\wait($firewall->process($payload));

        self::assertInstanceOf(DoNothing::class, $action);
    }

    /** @test */
    public function short_circuits_rule_evaluation_after_first_violation(): void
    {
        $payload = $this->createMock(PayloadInterface::class);
        $fail = $this->createMock(Rule::class);

        $fail->method('enforce')->willReturn(new Failure(new RuleViolation($fail)));
        $fail->method('getName')->willReturn('test:fail');

        $noop = $this->createMock(Rule::class);
        $noop->expects($this->never())->method('enforce');
        $noop->method('getName')->willReturn('test:noop');

        $firewall = new Firewall(new NullLogger());
        $firewall->addRule($fail);
        $firewall->addRule($fail);

        $action = Promise\wait($firewall->process($payload));

        self::assertInstanceOf(DoNothing::class, $action);
    }
}
