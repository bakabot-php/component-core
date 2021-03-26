<?php

declare(strict_types = 1);

namespace Bakabot\Component;

class DependentDummy extends AbstractComponent implements DependentComponentInterface
{
    protected function getParameters(): array
    {
        return [];
    }

    protected function getServices(): array
    {
        return [];
    }

    public function getDependencies(): array
    {
        return [
            DependencyDummy::class,
        ];
    }
}
