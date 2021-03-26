<?php

declare(strict_types = 1);

namespace Bakabot\Action;

final class DoNothing implements ActionInterface
{
    use TriggerMessageTrait;
}
