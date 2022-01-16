<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Amp\Promise;
use Amp\Success;
use Bakabot\Action\DoNothing;
use Bakabot\Command\Attribute\ArgumentExpression;
use Bakabot\Command\Attribute\SupportedEnvironment;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

#[ArgumentExpression('[foo:string]')]
#[SupportedEnvironment('discord')]
#[SupportedEnvironment('twitch')]
class TestCommand extends AbstractCommand
{
    public function args(): array
    {
        return $this->arguments();
    }

    public function run(): Promise
    {
        return new Success(new DoNothing());
    }
}

class AbstractCommandTest extends TestCase
{
    private function createTestPayload(string $requestedCommand = ''): BasePayload
    {
        return new BasePayload(
            $this->createMock(PayloadInterface::class),
            '!',
            HelpCommand::NAME,
            $requestedCommand !== '' ? [$requestedCommand] : [],
            $requestedCommand
        );
    }

    /** @test */
    public function common_accessors(): void
    {
        $command = new HelpCommand(new Commands());
        $command->bind($this->createTestPayload('ping'));

        self::assertSame('[command:string]', $command->argumentExpression());
        self::assertSame('{{ help.description }}', $command->description());
        self::assertSame('{{ help.flavor_text }}', $command->helpText());
        self::assertSame([], $command->supportedEnvironments());
        self::assertSame(HelpCommand::NAME, (string) $command);
    }

    /** @test */
    public function merges_all_supported_environment_attributes(): void
    {
        $command = new TestCommand();
        $command->bind($this->createTestPayload('bar'));

        self::assertSame('[foo:string]', $command->argumentExpression());
        self::assertSame(['foo' => 'bar'], $command->args());
        self::assertEquals(['discord', 'twitch'], $command->supportedEnvironments());
    }
}
