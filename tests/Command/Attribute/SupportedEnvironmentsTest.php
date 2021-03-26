<?php

declare(strict_types = 1);

namespace Bakabot\Command\Attribute;

class SupportedEnvironmentsTest extends AbstractAttributeTest
{
    protected const ATTRIBUTE = SupportedEnvironments::class;
    protected const VALUE = ['discord', 'twitch'];
}
