<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

class HelpTextTest extends AbstractAttributeTest
{
    protected const ATTRIBUTE = HelpText::class;
    protected const VALUE = "How would I help? I'm just a text.";
}
