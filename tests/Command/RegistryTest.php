<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Action\ActionInterface;
use Bakabot\Action\DoNothing;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    private function getTestCommand(string $environment = 'discord'): CommandInterface
    {
        return new class($environment) extends AbstractCommand {
            private string $environment;

            public function __construct(string $environment)
            {
                $this->environment = $environment;
            }

            public function getName(): string
            {
                return 'test';
            }

            public function getSupportedEnvironments(): array
            {
                return [$this->environment];
            }

            public function run(): ActionInterface
            {
                return new DoNothing();
            }
        };
    }

    /** @test */
    public function empty_registry_is_considered_null_iterator(): void
    {
        $registry = new Registry();

        self::assertSame(0, iterator_count($registry));
    }

    /** @test */
    public function registering_command_returns_it_during_iteration(): void
    {
        $command = $this->getTestCommand();

        $registry = new Registry();
        $registry->addCommand($command);

        self::assertSame(1, iterator_count($registry));
        self::assertSame(['discord:test' => $command], iterator_to_array($registry));
    }

    /** @test */
    public function registering_command_twice_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $command = $this->getTestCommand();

        $registry = new Registry();
        $registry->addCommand($command);
        $registry->addCommand($command);
    }

    /** @test */
    public function filtering_by_names_removes_non_matches(): void
    {
        $command = $this->getTestCommand();

        $registry = new Registry();
        $registry->addCommand($command);

        $emptyRegistry = $registry->filterByNames(['does-not-exist']);

        self::assertCount(0, $emptyRegistry);
    }

    /** @test */
    public function filtering_by_environment_keeps_matches_and_strips_environment(): void
    {
        $command = $this->getTestCommand();

        $registry = new Registry();
        $registry->addCommand($command);

        $filteredRegistry = $registry->filterByEnvironment('discord');

        self::assertCount(1, $filteredRegistry);
        self::assertSame([$command->getName() => $command], iterator_to_array($filteredRegistry));
    }

    /** @test */
    public function filtering_by_names_keeps_matches(): void
    {
        $command = $this->getTestCommand();

        $registry = new Registry();
        $registry->addCommand($command);

        $filteredRegistry = $registry->filterByEnvironment('discord');
        $filteredRegistry = $filteredRegistry->filterByNames([$command->getName()]);

        self::assertCount(1, $filteredRegistry);
        self::assertSame([$command->getName() => $command], iterator_to_array($filteredRegistry));
    }

    /** @test */
    public function merging_performs_deep_copy_of_registered_commands(): void
    {
        $discordCommand = $this->getTestCommand('discord');
        $twitchCommand = $this->getTestCommand('twitch');

        $registry1 = new Registry();
        $registry1->addCommand($discordCommand);

        $registry2 = new Registry();
        $registry2->addCommand($twitchCommand);

        self::assertEquals(
            $registry1->merge($registry2),
            $registry2->merge($registry1)
        );
    }
}
