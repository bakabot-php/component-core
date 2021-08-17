<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Promise;
use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\MessageInterface;
use Bakabot\Command\Payload as CommandPayload;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\EnvironmentInterface;
use Bakabot\Payload\Payload;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    private function createPayloadWithMessage(string $content): Payload
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getContent')->willReturn($content);

        return new Payload(
            $this->createMock(EnvironmentInterface::class),
            $this->createMock(ChannelInterface::class),
            $message,
            null,
        );
    }

    /** @test */
    public function returns_original_payload_if_prefix_not_found(): void
    {
        $parser = new CommandParser(new Prefix('!'));

        $payload = $this->createPayloadWithMessage('Hello World!');
        $returnedPayload = Promise\wait($parser->process($payload));

        self::assertSame($payload, $returnedPayload);
    }

    public function provideCommandMessages(): array
    {
        return [
            'simple' => [
                'message' => '!hello',
                'name' => 'hello',
                'parsedArguments' => [],
                'rawArguments' => '',
            ],

            'with_argument' => [
                'message' => '!hello World',
                'name' => 'hello',
                'parsedArguments' => ['World'],
                'rawArguments' => 'World',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideCommandMessages
     */
    public function creates_command_payload_if_prefix_found(
        string $message,
        string $name,
        array $parsedArguments,
        string $rawArguments
    ): void {
        $prefix = '!';
        $parser = new CommandParser(new Prefix($prefix));

        $payload = $this->createPayloadWithMessage($message);
        $returnedPayload = Promise\wait($parser->process($payload));

        self::assertNotSame($payload, $returnedPayload);

        self::assertInstanceOf(CommandPayload::class, $returnedPayload);
        self::assertSame($prefix, $returnedPayload->getCommandPrefix());
        self::assertSame($name, $returnedPayload->getCommandName());
        self::assertSame($parsedArguments, $returnedPayload->getParsedArguments());
        self::assertSame($rawArguments, $returnedPayload->getRawArguments());
    }
}
