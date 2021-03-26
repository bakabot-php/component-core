<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class Collection implements IteratorAggregate
{
    /** @var ComponentInterface[] */
    private array $components = [];

    private function registerDependencies(ComponentInterface $component): void
    {
        if (!($component instanceof DependentComponentInterface)) {
            return;
        }

        foreach ($component->getDependencies() as $dependency) {
            if (!$this->has($dependency)) {
                $this->push(new $dependency());
            }
        }
    }

    /**
     * @return ArrayIterator<array-key, ComponentInterface>
     */
    public function getIterator(): Traversable
    {
        reset($this->components);

        return new ArrayIterator(array_values($this->components));
    }

    /**
     * @param ComponentInterface|class-string<ComponentInterface> $component
     * @return bool
     */
    public function has(ComponentInterface|string $component): bool
    {
        $class = is_object($component) ? get_class($component) : $component;

        return isset($this->components[$class]);
    }

    public function push(ComponentInterface $component): void
    {
        if ($this->has($component)) {
            return;
        }

        $this->registerDependencies($component);

        $this->components[get_class($component)] = $component;
    }
}
