<?php

declare(strict_types = 1);

namespace Bakabot\Command;

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
        return $this->getArguments();
    }

    public function run(): DoNothing
    {
        return new DoNothing();
    }
}

class AbstractCommandTest extends TestCase
{
    private function createTestPayload(string $requestedCommand = ''): Payload
    {
        return new Payload(
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
        $command = new HelpCommand(new Collection());
        $command->bind($this->createTestPayload('ping'));

        self::assertSame('[command:string]', $command->getArgumentExpression());
        self::assertSame('{{ help.description }}', $command->getDescription());
        self::assertSame('{{ help.flavor_text }}', $command->getHelpText());
        self::assertSame([], $command->getSupportedEnvironments());
        self::assertSame(HelpCommand::NAME, (string) $command);
    }

    /** @test */
    public function merges_all_supported_environment_attributes(): void
    {
        $command = new TestCommand();
        $command->bind($this->createTestPayload('bar'));

        self::assertSame('[foo:string]', $command->getArgumentExpression());
        self::assertSame(['foo' => 'bar'], $command->args());
        self::assertEquals(['discord', 'twitch'], $command->getSupportedEnvironments());
    }
}
