<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Action\ActionInterface;
use Bakabot\Action\DoNothing;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
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
    public function empty_collection_is_considered_null_iterator(): void
    {
        $collection = new Collection();

        self::assertSame(0, iterator_count($collection));
    }

    /** @test */
    public function pushing_command_returns_it_during_iteration(): void
    {
        $command = $this->getTestCommand();

        $collection = new Collection();
        $collection->push($command->getName(), $command);

        self::assertSame(1, iterator_count($collection));
        self::assertSame([$command->getName() => $command], iterator_to_array($collection));
    }

    /** @test */
    public function can_find_commands_by_name(): void
    {
        $command = $this->getTestCommand();

        $collection = new Collection();
        $collection->push($command->getName(), $command);

        self::assertSame($command, $collection->findByName($command->getName()));
        self::assertNull($collection->findByName('does-not-exist'));
    }
}
