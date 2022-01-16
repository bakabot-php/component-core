<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall\Rule;

use Amp\Promise;
use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\User\User;
use Bakabot\Environment;
use Bakabot\Payload\BasePayload;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\RuleViolation;
use PHPUnit\Framework\TestCase;

class IgnoreDirectMessagesTest extends TestCase
{
    private function createPayload(bool $isPrivate): PayloadInterface
    {
        $environment = $this->createMock(Environment::class);
        $environment->method('getName')->willReturn('discord');

        $channel = $this->createMock(Channel::class);
        $channel->method('isPrivate')->willReturn($isPrivate);

        return new BasePayload(
            $environment,
            $channel,
            $this->createMock(Message::class),
            null,
        );
    }

    /** @test */
    public function throws_rule_violation_for_direct_messages(): void
    {
        $payload = $this->createPayload(true);
        $rule = new IgnoreDirectMessages();

        try {
            Promise\wait($rule->enforce($payload));
        } catch (RuleViolation $violation) {
            self::assertSame('Failed firewall rule [internal:ignore-direct-messages]', $violation->getMessage());
            self::assertSame('Ignoring direct message', $rule->getDetailedMessage($payload));
        }
    }

    /** @test */
    public function returns_original_payload_for_normal_users(): void
    {
        $payload = $this->createPayload(false);

        $rule = new IgnoreDirectMessages();
        $returnedPayload = Promise\wait($rule->enforce($payload));

        self::assertSame($payload, $returnedPayload);
    }
}
