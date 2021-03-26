<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Chat\Server\ServerInterface;
use Bakabot\Command\Payload as CommandPayload;
use Bakabot\EnvironmentInterface;
use Bakabot\Payload\Payload;
use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $payload = new Payload(
            $environment = $this->createMock(EnvironmentInterface::class),
            $channel = $this->createMock(ChannelInterface::class),
            $message = $this->createMock(MessageInterface::class),
            $server = $this->createMock(ServerInterface::class),
            null
        );

        $decoratedPayload = Payload::withAllowedCommands($payload, ['my_command']);

        $commandPayload = new CommandPayload(
            $decoratedPayload,
            $prefix = '!',
            $name = 'help',
            $parsedArguments = [],
            $rawArguments = ''
        );

        self::assertSame($environment, $commandPayload->getEnvironment());
        self::assertSame($channel, $commandPayload->getChannel());
        self::assertSame($message, $commandPayload->getMessage());
        self::assertSame($server, $commandPayload->getServer());
        self::assertSame(['my_command'], $commandPayload->getAllowedCommands());

        self::assertSame($prefix, $commandPayload->getCommandPrefix());
        self::assertSame($name, $commandPayload->getCommandName());
        self::assertSame($parsedArguments, $commandPayload->getParsedArguments());
        self::assertSame($rawArguments, $commandPayload->getRawArguments());
    }
}
