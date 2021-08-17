<?php

declare(strict_types = 1);

namespace Bakabot\Command\Argument;

use BadMethodCallException;
use Bakabot\Command\Payload as CommandPayload;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Definition extends OptionsResolver
{
    private int $argumentCount = 0;
    private array $argumentMap = [];
    private ?int $greedyArgumentIndex = null;

    /**
     * @param string|array<array-key, string> $options
     */
    private function registerArguments(string|array $options): void
    {
        foreach ((array) $options as $option) {
            if (!isset($this->argumentMap[$option])) {
                $this->argumentMap[$option] = $this->argumentCount++;
            }
        }
    }

    public function clear(): self
    {
        throw new BadMethodCallException();
    }

    public function getGreedyArgumentIndex(): ?int
    {
        return $this->greedyArgumentIndex;
    }

    public function remove($optionNames): self
    {
        throw new BadMethodCallException();
    }

    /**
     * @param CommandPayload $payload
     * @return array<string, string>
     */
    public function resolveArguments(CommandPayload $payload): array
    {
        $argumentMap = array_flip($this->argumentMap);
        $defined = $this->getDefinedOptions();

        if ($this->greedyArgumentIndex === null && count($defined) === 1) {
            $argument = reset($defined);

            $value = $argument === 'message'
                ? $payload->getRawArguments()
                : $payload->getParsedArguments()[0] ?? null;

            $options = [$argument => $value];
        } elseif ($this->greedyArgumentIndex !== null) {
            $arguments = $payload->getParsedArguments();

            $options = [];
            for ($i = 0; $i < $this->greedyArgumentIndex; ++$i) {
                $options[$argumentMap[$i]] = $arguments[$i];
                unset($argumentMap[$i], $arguments[$i]);
            }

            $greedyArgument = $argumentMap[$this->greedyArgumentIndex];
            $options[$greedyArgument] = array_values($arguments);
        } else {
            $parsedArguments = $payload->getParsedArguments();

            $actualCount = count($parsedArguments);
            $expectedCount = count($argumentMap);

            if ($actualCount !== $expectedCount) {
                // case 1: implode overhang back into one value
                if ($actualCount > $expectedCount) {
                    $overhangIndex = ($expectedCount - 1);
                    $overhang = implode(' ', array_slice($parsedArguments, $overhangIndex));

                    $parsedArguments = array_slice($parsedArguments, 0, $overhangIndex);
                    $parsedArguments[$overhangIndex] = $overhang;
                } // case 2: null-fill missing keys
                else {
                    for ($i = 0; $i < $expectedCount; $i++) {
                        if (!isset($parsedArguments[$i])) {
                            $parsedArguments[$i] = null;
                        }
                    }
                }
            }

            $options = array_combine($argumentMap, $parsedArguments);
        }

        /** @var array<string, string> $options */
        $resolvedOptions = $this->resolve($options);

        /** @var array<string, string> $resolvedOptions */
        return $resolvedOptions;
    }

    /**
     * @param string|string[] $optionNames
     * @return $this
     */
    public function setDefined($optionNames): self
    {
        $this->registerArguments($optionNames);

        return parent::setDefined($optionNames);
    }

    public function setGreedyArgumentIndex(int $greedyArgumentIndex): void
    {
        $this->greedyArgumentIndex = $greedyArgumentIndex;
    }

    /**
     * @param string|string[] $optionNames
     * @return $this
     */
    public function setRequired($optionNames): self
    {
        $this->registerArguments($optionNames);

        return parent::setRequired($optionNames);
    }
}
