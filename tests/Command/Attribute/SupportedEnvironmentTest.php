<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

class SupportedEnvironmentTest extends AbstractAttributeTest
{
    protected const ATTRIBUTE = SupportedEnvironment::class;
    protected const VALUE = 'discord';
}
