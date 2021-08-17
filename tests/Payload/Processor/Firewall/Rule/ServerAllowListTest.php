<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall\Rule;

use Amp\Promise;
use Amp\Success;
use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\ServerInterface;
use Bakabot\Chat\Server\Settings\AllowedCommands;
use Bakabot\Chat\Server\Settings\ChannelList;
use Bakabot\Chat\Server\Settings\ServerSettings;
use Bakabot\Chat\Server\Settings\ServerSettingsSourceInterface;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\EnvironmentInterface;
use Bakabot\Payload\Payload;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\RuleViolation;
use PHPUnit\Framework\TestCase;

class ServerAllowListTest extends TestCase
{
    private function createPayload(
        string $channelId,
        ?string $channelName = null,
        bool $withServer = true
    ): PayloadInterface {
        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getName')->willReturn('discord');

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getId')->willReturn($channelId);
        $channel->method('getName')->willReturn($channelName);

        $server = null;
        if ($withServer) {
            $server = $this->createMock(ServerInterface::class);
            $server->method('getName')->willReturn('Among Us');
        }

        return new Payload(
            $environment,
            $channel,
            $this->createMock(MessageInterface::class),
            $server
        );
    }

    private function createSettingsSource(string ...$allowedChannels): ServerSettingsSourceInterface
    {
        return new class($allowedChannels) implements ServerSettingsSourceInterface {
            public function __construct(private array $allowedChannels)
            {
            }

            public function getServerSettings(PayloadInterface $payload): Promise
            {
                return new Success(
                    new ServerSettings(
                        new Language('en'),
                        new Prefix('!'),
                        new AllowedCommands(),
                        new ChannelList($this->allowedChannels),
                        new ChannelList()
                    )
                );
            }
        };
    }

    /** @test */
    public function throws_rule_violation_for_channels_not_on_allow_list(): void
    {
        $payload = $this->createPayload('2');
        $rule = new ServerAllowList($this->createSettingsSource('1'));

        try {
            Promise\wait($rule->enforce($payload));
        } catch (RuleViolation $violation) {
            self::assertSame('Failed firewall rule [server:allow-list]', $violation->getMessage());
            self::assertSame("Ignoring message in Discord channel [2] (server: [Among Us]) - not on server's allow list", $rule->getDetailedMessage($payload));
        }
    }

    /** @test */
    public function returns_original_payload_for_direct_messages(): void
    {
        $payload = $this->createPayload('1', null, false);
        $rule = new ServerAllowList($this->createSettingsSource());

        $returnedPayload = Promise\wait($rule->enforce($payload));

        self::assertSame($payload, $returnedPayload);
    }

    /** @test */
    public function returns_original_payload_for_allowed_channels(): void
    {
        $payload = $this->createPayload('1');
        $rule = new ServerAllowList($this->createSettingsSource('1'));

        $returnedPayload = Promise\wait($rule->enforce($payload));

        self::assertSame($payload, $returnedPayload);
    }
}
