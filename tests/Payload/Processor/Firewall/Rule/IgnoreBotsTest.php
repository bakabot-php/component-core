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

class IgnoreBotsTest extends TestCase
{
    private function createPayload(bool $isBot): PayloadInterface
    {
        $environment = $this->createMock(Environment::class);
        $environment->method('getName')->willReturn('discord');

        $author = $this->createMock(User::class);
        $author->method('getId')->willReturn('1234');
        $author->method('isBot')->willReturn($isBot);

        $message = $this->createMock(Message::class);
        $message->method('getAuthor')->willReturn($author);

        return new BasePayload(
            $environment,
            $this->createMock(Channel::class),
            $message,
            null,
        );
    }

    /** @test */
    public function throws_rule_violation_for_bots(): void
    {
        $payload = $this->createPayload(true);
        $rule = new IgnoreBots();

        try {
            Promise\wait($rule->enforce($payload));
        } catch (RuleViolation $violation) {
            self::assertSame('Failed firewall rule [internal:ignore-bots]', $violation->getMessage());
            self::assertSame('Ignoring message from Discord bot with user ID [1234]', $rule->getDetailedMessage($payload));
        }
    }

    /** @test */
    public function returns_original_payload_for_normal_users(): void
    {
        $payload = $this->createPayload(false);

        $rule = new IgnoreBots();
        $returnedPayload = Promise\wait($rule->enforce($payload));

        self::assertSame($payload, $returnedPayload);
    }
}
