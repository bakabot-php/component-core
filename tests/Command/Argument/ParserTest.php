<?php

declare(strict_types = 1);

namespace Bakabot\Command\Argument;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function getValidExpressions(): array
    {
        return [
            'empty' => [
                'expression' => '',
                'defined_options' => [],
                'required_options' => [],
                'greedy_argument_index' => null,
            ],

            'required single argument' => [
                'expression' => 'name',
                'defined_options' => ['name'],
                'required_options' => ['name'],
                'greedy_argument_index' => null,
            ],

            'required single typed argument' => [
                'expression' => 'name:string',
                'defined_options' => ['name'],
                'required_options' => ['name'],
                'greedy_argument_index' => null,
            ],

            'required single argument array' => [
                'expression' => 'names*',
                'defined_options' => ['names'],
                'required_options' => ['names'],
                'greedy_argument_index' => 0,
            ],

            'required single typed argument array' => [
                'expression' => 'names:string*',
                'defined_options' => ['names'],
                'required_options' => ['names'],
                'greedy_argument_index' => 0,
            ],

            'optional single argument' => [
                'expression' => '[name]',
                'defined_options' => ['name'],
                'required_options' => [],
                'greedy_argument_index' => null,
            ],

            'optional single typed argument' => [
                'expression' => '[name:string]',
                'defined_options' => ['name'],
                'required_options' => [],
                'greedy_argument_index' => null,
            ],

            'optional single argument array' => [
                'expression' => '[names]*',
                'defined_options' => ['names'],
                'required_options' => [],
                'greedy_argument_index' => 0,
            ],

            'optional single typed argument array' => [
                'expression' => '[names:string]*',
                'defined_options' => ['names'],
                'required_options' => [],
                'greedy_argument_index' => 0,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getValidExpressions
     */
    public function correctly_parses_expressions(
        string $expression,
        array $definedOptions,
        array $requiredOptions,
        ?int $greedyArgumentIndex
    ): void  {
        $definition = (new Parser())->parse($expression);

        self::assertEquals($definedOptions, $definition->getDefinedOptions());
        self::assertEquals($requiredOptions, $definition->getRequiredOptions());
        self::assertSame($greedyArgumentIndex, $definition->greedyArgumentPosition());
    }

    /** @test */
    public function invalid_type_expression_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid token [name:string:ehh].');

        (new Parser())->parse('name:string:ehh');
    }
}
