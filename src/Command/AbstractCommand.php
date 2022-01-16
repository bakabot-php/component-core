<?php

declare(strict_types = 1);

namespace Bakabot\Command;

use Amp\Promise;
use Bakabot\Action\Action;
use Bakabot\Action\Reply;
use Bakabot\Attribute\AttributeValueGetter;
use Bakabot\Attribute\Exception\MissingAttributeException;
use Bakabot\Command\Argument\Definition;
use Bakabot\Command\Argument\Parser as DefinitionParser;
use Bakabot\Command\Attribute as Cmd;
use Bakabot\Command\Trigger\PrefixedMessage;
use Bakabot\Component\Core\Amp\Promise\Promisor;
use ReflectionException;

abstract class AbstractCommand implements Command
{
    use Promisor;

    private ?Definition $argumentDefinition = null;
    /** @var array<string, string> */
    private array $arguments = [];
    /** @var string[] */
    private ?array $supportedEnvironments = null;

    /** @psalm-suppress PropertyNotSetInConstructor */
    protected Payload $payload;

    private function argumentDefinition(): Argument\Definition
    {
        if ($this->argumentDefinition === null) {
            $this->argumentDefinition = $this->createArgumentDefinition();
        }

        return $this->argumentDefinition;
    }

    protected function createArgumentDefinition(): Definition
    {
        return (new DefinitionParser())->parse($this->argumentExpression());
    }

    final protected function argument(string $name): mixed
    {
        return $this->arguments[$name];
    }

    /** @return array<string, string> */
    final protected function arguments(): array
    {
        return $this->arguments;
    }

    final protected function prefix(): ?string
    {
        $trigger = $this->payload->trigger;

        if ($trigger instanceof PrefixedMessage) {
            return $trigger->prefix();
        }

        return null;
    }

    final protected function reply(): Reply
    {
        return $this->payload->message->environment->replier($this->payload->message);
    }

    final public function bind(Payload $payload): void
    {
        $this->arguments = $this->argumentDefinition()->resolveArguments($payload);
        $this->payload = $payload;
    }

    public function argumentExpression(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\ArgumentExpression::class, '');
    }

    public function description(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\Description::class);
    }

    public function helpText(): string
    {
        return (string) AttributeValueGetter::getAttributeValue(
            $this,
            Cmd\HelpText::class,
            fn() => $this->description()
        );
    }

    public function name(): string
    {
        return (string) AttributeValueGetter::getAttributeValue($this, Cmd\Name::class);
    }

    abstract protected function execute(): Action;

    /**
     * @return Promise<Action>
     */
    public function run(): Promise
    {
        return $this->promise($this->execute());
    }

    /**
     * @throws MissingAttributeException
     * @throws ReflectionException
     * @return string[]
     */
    public function supportedEnvironments(): array
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
        return $this->name();
    }
}
