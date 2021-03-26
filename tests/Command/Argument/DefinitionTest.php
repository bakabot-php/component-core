<?php

declare(strict_types = 1);

namespace Bakabot\Command\Argument;

use BadMethodCallException;
use Bakabot\Command\Payload;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    /** @test */
    public function forbidden_to_clear_definition(): void
    {
        $this->expectException(BadMethodCallException::class);

        $definition = new Definition();
        $definition->clear();
    }

    /** @test */
    public function forbidden_to_remove_options_from_definition(): void
    {
        $this->expectException(BadMethodCallException::class);

        $definition = new Definition();
        $definition->remove('me');
    }

    private function createPayload(
        array $parsedArguments,
        string $rawArguments
    ): Payload {
        return new Payload(
            $this->createMock(PayloadInterface::class),
            '!',
            'test',
            $parsedArguments,
            $rawArguments
        );
    }

    public function getValidExpressions(): array
    {
        return [
            'empty' => [
                'expression' => '',
                'parsed_arguments' => [],
                'raw_arguments' => '',
                'expected_data' => [],
            ],

            'required single argument' => [
                'expression' => 'name',
                'parsed_arguments' => ['Nayleen'],
                'raw_arguments' => 'Nayleen',
                'expected_data' => ['name' => 'Nayleen'],
            ],

            'required single typed argument' => [
                'expression' => 'name:string',
                'parsed_arguments' => ['Nayleen'],
                'raw_arguments' => 'Nayleen',
                'expected_data' => ['name' => 'Nayleen'],
            ],

            'required message argument' => [
                'expression' => 'message',
                'parsed_arguments' => ['Lorem ipsum dolor sit amet'],
                'raw_arguments' => 'Lorem ipsum dolor sit amet',
                'expected_data' => ['message' => 'Lorem ipsum dolor sit amet'],
            ],

            'one required, one optional argument, partially filled' => [
                'expression' => 'user [greeting]',
                'parsed_arguments' => ['Nayleen'],
                'raw_arguments' => 'Nayleen',
                'expected_data' => [
                    'user' => 'Nayleen',
                    'greeting' => null,
                ],
            ],

            'one required, one optional argument, all filled' => [
                'expression' => 'user [greeting]',
                'parsed_arguments' => ['Nayleen', 'Hi!'],
                'raw_arguments' => 'Nayleen Hi!',
                'expected_data' => [
                    'user' => 'Nayleen',
                    'greeting' => 'Hi!',
                ],
            ],

            'greedy required first argument' => [
                'expression' => 'names*',
                'parsed_arguments' => ['Nayleen', 'Minaire', 'Naihla'],
                'raw_arguments' => 'Nayleen Minaire Shinayna',
                'expected_data' => [
                    'names' => ['Nayleen', 'Minaire', 'Naihla'],
                ],
            ],

            'greedy second argument' => [
                'expression' => 'action names*',
                'parsed_arguments' => ['scold', 'Huey', 'Dewey', 'Louie'],
                'raw_arguments' => 'scold Huey Dewey Louie',
                'expected_data' => [
                    'action' => 'scold',
                    'names' => ['Huey', 'Dewey', 'Louie'],
                ],
            ],

            'non-greedy argument with overhang' => [
                'expression' => 'user_or_quote:string [quote:string]',
                'parsed_arguments' => ['@elonmusk', 'If', 'there’s', 'ever', 'a', 'scandal', 'about', 'me,', '*please*', 'call', 'it', 'Elongate'],
                'raw_arguments' => '@elonmusk If there’s ever a scandal about me, *please* call it Elongate',
                'expected_data' => [
                    'user_or_quote' => '@elonmusk',
                    'quote' => 'If there’s ever a scandal about me, *please* call it Elongate',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getValidExpressions
     */
    public function correctly_resolves_arguments(
        string $expression,
        array $parsedArguments,
        string $rawArguments,
        array $expectedData
    ): void {
        $definition = (new Parser())->parse($expression);

        $resolvedData = $definition->resolveArguments($this->createPayload($parsedArguments, $rawArguments));

        self::assertEquals($expectedData, $resolvedData);
    }
}
