<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

class DescriptionTest extends AbstractAttributeTest
{
    protected const ATTRIBUTE = Description::class;
    protected const VALUE = 'Does absolutely nothing.';
}
