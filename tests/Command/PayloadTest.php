<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Chat\Channel\Channel;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Server\Server;
use Bakabot\Command\BasePayload as CommandPayload;
use Bakabot\Environment;
use Bakabot\Payload\BasePayload;
use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{
    /** @test */
    public function acts_as_dto(): void
    {
        $payload = new BasePayload(
            $environment = $this->createMock(Environment::class),
            $channel = $this->createMock(Channel::class),
            $message = $this->createMock(Message::class),
            $server = $this->createMock(Server::class),
        );

        $commandPayload = new CommandPayload(
            $payload,
            $prefix = '!',
            $name = 'help',
            $parsedArguments = [],
            $rawArguments = ''
        );

        self::assertSame($environment, $commandPayload->getEnvironment());
        self::assertSame($channel, $commandPayload->getChannel());
        self::assertSame($message, $commandPayload->getMessage());
        self::assertSame($server, $commandPayload->getServer());

        self::assertSame($prefix, $commandPayload->getCommandPrefix());
        self::assertSame($name, $commandPayload->getCommandName());
        self::assertSame($parsedArguments, $commandPayload->getParsedArguments());
        self::assertSame($rawArguments, $commandPayload->getRawArguments());
    }
}
