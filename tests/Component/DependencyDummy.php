<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use stdClass;

class DependencyDummy extends AbstractComponent
{
    protected function getParameters(): array
    {
        return [
            'hello' => 'world',
        ];
    }

    protected function getServices(): array
    {
        return [
            'my_service' => fn () => new stdClass(),
        ];
    }
}
