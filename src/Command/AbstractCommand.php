<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Bakabot\Attribute\AttributeValueGetter;
use Bakabot\Attribute\Exception\MissingAttributeException;
use Bakabot\Command\Argument\Definition;
use Bakabot\Command\Argument\Parser as DefinitionParser;
use Bakabot\Command\Attribute as Cmd;
use Bakabot\Component\Core\Amp\Promise\Promisor;
use ReflectionException;

abstract class AbstractCommand implements CommandInterface
{
    use Promisor;

    private ?Definition $argumentDefinition = null;
    /** @var array<string, string> */
    private array $arguments = [];
    private ?Payload $payload = null;
    /** @var string[] */
    private ?array $supportedEnvironments = null;

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

    /** @return array<string, string> */
    final protected function getArguments(): array
    {
        return $this->arguments;
    }

    final protected function getCommandPrefix(): string
    {
        return $this->getPayload()->getCommandPrefix();
    }

    final protected function getPayload(): Payload
    {
        assert($this->payload !== null);

        return $this->payload;
    }

    final public function bind(Payload $payload): void
    {
        $this->arguments = $this->getArgumentDefinition()->resolveArguments($payload);
        $this->payload = $payload;
    }

    public function getArgumentExpression(): string
    {
        return (string)AttributeValueGetter::getAttributeValue($this, Cmd\ArgumentExpression::class, '');
    }

    public function getDescription(): string
    {
        return (string)AttributeValueGetter::getAttributeValue($this, Cmd\Description::class);
    }

    public function getHelpText(): string
    {
        return (string)AttributeValueGetter::getAttributeValue(
            $this,
            Cmd\HelpText::class,
            fn() => $this->getDescription()
        );
    }

    public function getName(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\Name::class);
    }

    /**
     * @return string[]
     * @throws MissingAttributeException
     * @throws ReflectionException
     */
    public function getSupportedEnvironments(): array
    {
        if ($this->supportedEnvironments === null) {
            /** @var string[] $supportedEnvironments */
            $supportedEnvironments = array_values(
                (array) AttributeValueGetter::getAttributeValue($this, Cmd\SupportedEnvironment::class, null)
            );

            $this->supportedEnvironments = array_values(array_unique($supportedEnvironments));
        }

        return $this->supportedEnvironments;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
