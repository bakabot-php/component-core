<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall\Rule;

use Amp\Failure;
use Amp\Promise;
use Amp\Success;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\RuleViolation;

final class IgnoreDirectMessages implements RuleInterface
{
    public function getDetailedMessage(PayloadInterface $payload): string
    {
        return 'Ignoring direct message';
    }

    /**
     * @param PayloadInterface $payload
     * @return Failure<RuleViolation>|Promise<PayloadInterface>
     */
    public function enforce(PayloadInterface $payload): Failure|Promise
    {
        if ($payload->getChannel()->isPrivate()) {
            /** @var Failure<RuleViolation> $failure */
            $failure = new Failure(new RuleViolation($this));
            return $failure;
        }

        /** @var Success<PayloadInterface> $success */
        $success = new Success($payload);
        return $success;
    }

    public function getName(): string
    {
        return RuleInterface::PREFIX_INTERNAL . 'ignore-direct-messages';
    }
}
