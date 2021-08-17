<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall;

use Amp\Promise;
use Bakabot\Action\DoNothing;
use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\User\UserInterface;
use Bakabot\EnvironmentInterface;
use Bakabot\Payload\Payload;
use Bakabot\Payload\Processor\Firewall\Rule\IgnoreBots;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

class FirewallIntegrationTest extends TestCase
{
    /** @test */
    public function will_log_detailed_message_for_violations(): void
    {
        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getName')->willReturn('discord');

        $author = $this->createMock(UserInterface::class);
        $author->method('getId')->willReturn('1234');
        $author->method('isBot')->willReturn(true);

        $message = $this->createMock(MessageInterface::class);
        $message->method('getAuthor')->willReturn($author);

        $payload = new Payload(
            $environment,
            $this->createMock(ChannelInterface::class),
            $message,
            null,
        );

        $logger = new TestLogger();
        $firewall = new Firewall($logger);

        $rule = new IgnoreBots();
        $firewall->addRule($rule);

        $action = Promise\wait($firewall->process($payload));

        self::assertInstanceOf(DoNothing::class, $action);
        self::assertTrue($logger->hasDebug($rule->getDetailedMessage($payload)));
    }
}
