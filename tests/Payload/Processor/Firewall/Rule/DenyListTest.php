<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall\Rule;

use Amp\Promise;
use Amp\Success;
use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Server;
use Bakabot\Chat\Server\Settings\AllowedCommands;
use Bakabot\Chat\Server\Settings\ChannelList;
use Bakabot\Chat\Server\Settings\Settings;
use Bakabot\Chat\Server\Settings\SettingsSource;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Environment;
use Bakabot\Payload\BasePayload;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\RuleViolation;
use PHPUnit\Framework\TestCase;

class ServerDenyListTest extends TestCase
{
    private function createPayload(
        string $channelId,
        ?string $channelName = null,
        bool $withServer = true
    ): PayloadInterface {
        $environment = $this->createMock(Environment::class);
        $environment->method('getName')->willReturn('discord');

        $channel = $this->createMock(Channel::class);
        $channel->method('getId')->willReturn($channelId);
        $channel->method('getName')->willReturn($channelName);

        $server = null;
        if ($withServer) {
            $server = $this->createMock(Server::class);
            $server->method('getName')->willReturn('Among Us');
        }

        return new BasePayload(
            $environment,
            $channel,
            $this->createMock(Message::class),
            $server
        );
    }

    private function createSettingsSource(string ...$deniedChannels): SettingsSource
    {
        return new class($deniedChannels) implements SettingsSource {
            public function __construct(private array $deniedChannels)
            {
            }

            public function settings(PayloadInterface $message): Promise
            {
                return new Success(
                    new Settings(
                        new Language('en'),
                        new Prefix('!'),
                        new AllowedCommands(),
                        new ChannelList(),
                        new ChannelList($this->deniedChannels)
                    )
                );
            }
        };
    }

    /** @test */
    public function throws_rule_violation_for_channels_on_deny_list(): void
    {
        $payload = $this->createPayload('1');
        $rule = new ServerDenyList($this->createSettingsSource('1'));

        try {
            Promise\wait($rule->enforce($payload));
        } catch (RuleViolation $violation) {
            self::assertSame('Failed firewall rule [server:deny-list]', $violation->getMessage());
            self::assertSame("Ignoring message in Discord channel [1] (server: [Among Us]) - is on server's deny list", $rule->getDetailedMessage($payload));
        }
    }

    /** @test */
    public function returns_original_payload_for_direct_messages(): void
    {
        $payload = $this->createPayload('1', null, false);
        $rule = new ServerDenyList($this->createSettingsSource());

        $returnedPayload = Promise\wait($rule->enforce($payload));

        self::assertSame($payload, $returnedPayload);
    }

    /** @test */
    public function returns_original_payload_for_other_channels(): void
    {
        $payload = $this->createPayload('2');
        $rule = new ServerDenyList($this->createSettingsSource('1'));

        $returnedPayload = Promise\wait($rule->enforce($payload));

        self::assertSame($payload, $returnedPayload);
    }
}
