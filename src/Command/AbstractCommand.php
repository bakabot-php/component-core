<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Attribute\AttributeValueGetter;
use Bakabot\Command\Argument\Definition;
use Bakabot\Command\Argument\Parser as DefinitionParser;
use Bakabot\Command\Attribute as Cmd;

abstract class AbstractCommand implements CommandInterface
{
    private ?Definition $argumentDefinition = null;
    /** @var array<string, string> */
    private array $arguments = [];
    protected ?Payload $payload = null;

    protected function createArgumentDefinition(): Definition
    {
        return (new DefinitionParser())->parse($this->getArgumentExpression());
    }

    final protected function getArgument(string $name): mixed
    {
        return $this->arguments[$name];
    }

    final protected function getArgumentDefinition(): Argument\Definition
    {
        if ($this->argumentDefinition === null) {
            $this->argumentDefinition = $this->createArgumentDefinition();
        }

        return $this->argumentDefinition;
    }

    /**
     * @return array<string, string>
     */
    final protected function getArguments(): array
    {
        return $this->arguments;
    }

    final protected function getCommandPrefix(): string
    {
        assert($this->payload !== null);

        return $this->payload->getCommandPrefix();
    }

    final public function bind(Payload $payload): void
    {
        $this->arguments = $this->getArgumentDefinition()->resolveArguments($payload);
        $this->payload = $payload;
    }

    public function getArgumentExpression(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\ArgumentExpression::class, '');
    }

    public function getDescription(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\Description::class);
    }

    public function getHelpText(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\HelpText::class, fn () => $this->getDescription());
    }

    public function getName(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\Name::class);
    }

    /**
     * @return array<int, string>
     */
    public function getSupportedEnvironments(): array
    {
        return array_values((array) AttributeValueGetter::getAttributeValue($this, Cmd\SupportedEnvironments::class));
    }
}
