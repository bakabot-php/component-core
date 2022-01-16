<?php

declare(strict_types = 1);

namespace Bakabot\Command\Argument;

use InvalidArgumentException;

final class Parser
{
    private function detectType(string $token): array
    {
        $type = null;

        if (str_contains($token, ':') !== false) {
            assert(
                substr_count($token, ':') === 1,
                new InvalidArgumentException(sprintf('Invalid token [%s].', $token))
            );

            [$token, $type] = explode(':', $token);
        }

        return [$token, $type];
    }

    private function parseArgument(Definition $definition, string $token): bool
    {
        // "!hi [names]*" -> optional (possibly empty) array of names
        // "!hi [names:string]*" -> same, but names need to be strings
        if (str_ends_with($token, ']*')) {
            $name = trim($token, '[]*');
            [$name, $type] = $this->detectType($name);

            /** @var string $name */
            $type = $type ? "{$type}[]" : 'array';

            $definition
                ->setDefined($name)
                ->setDefault($name, [])
                ->setAllowedTypes($name, $type);

            return false;
        }

        // "!hi names*" -> required array of names
        // "!hi names:string*" -> same, but names need to be strings
        if (str_ends_with($token, '*')) {
            $name = trim($token, '*');
            [$name, $type] = $this->detectType($name);

            /** @var string $name */
            $type = $type ? "{$type}[]" : 'array';

            $definition
                ->setRequired($name)
                ->setAllowedTypes($name, $type);

            return false;
        }

        // "!hi [name]" -> optional "name" argument
        // "!hi [name:string]" -> same, but name needs to be string
        if (str_starts_with($token, '[')) {
            $name = trim($token, '[]');
            [$name, $type] = $this->detectType($name);

            /** @var string $name */
            $definition->setDefined($name);
            $definition->setDefault($name, null);

            if (is_string($type)) {
                $definition->setAllowedTypes($name, [$type, 'null']);
            }
        }
        // "!hi name" -> required "name" argument
        // "!hi name:string" -> same, but name needs to be a string
        else {
            $name = $token;
            [$name, $type] = $this->detectType($name);

            /** @var string $name */
            $definition->setRequired($name);

            if (is_string($type)) {
                $definition->setAllowedTypes($name, $type);
            }
        }

        return true;
    }

    public function parse(string $expression): Definition
    {
        $definition = new Definition();

        $tokens = explode(' ', $expression);
        $tokens = array_map('\trim', $tokens);
        $tokens = array_values(array_filter($tokens));

        if (count($tokens) === 0) {
            return $definition;
        }

        $greedyArgumentIndex = null;

        foreach ($tokens as $i => $token) {
            $continue = $this->parseArgument($definition, $token);

            if (!$continue) {
                $greedyArgumentIndex = $i;
                break;
            }
        }

        if ($greedyArgumentIndex !== null) {
            $definition->setGreedyArgumentPosition($greedyArgumentIndex);
        }

        return $definition;
    }
}
