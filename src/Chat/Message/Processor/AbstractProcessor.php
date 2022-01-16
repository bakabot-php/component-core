<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Processor;

use Bakabot\Component\Core\Amp\Promise\Promisor;

abstract class AbstractProcessor implements Processor
{
    use Promisor;
}
