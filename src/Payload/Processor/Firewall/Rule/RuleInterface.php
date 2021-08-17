<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall\Rule;

use Amp\Promise;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\RuleViolation;

interface RuleInterface
{
    public const PREFIX_INTERNAL = 'internal:';
    public const PREFIX_SERVER = 'server:';

    public function getDetailedMessage(PayloadInterface $payload): ?string;

    /**
     * Passes along the payload (and is able to return a decorated one).
     * Fails the promise with a @see RuleViolation if the rule is violated.
     *
     * @param PayloadInterface $payload
     * @return Promise
     */
    public function enforce(PayloadInterface $payload): Promise;

    public function getName(): string;
}
