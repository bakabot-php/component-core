<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Bakabot\Component\Core\Amp\Promise\Promisor;

abstract class AbstractProcessor implements ProcessorInterface
{
    use Promisor;
}
