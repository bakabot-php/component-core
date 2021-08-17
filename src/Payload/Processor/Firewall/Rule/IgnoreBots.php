<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall\Rule;

use Amp\Failure;
use Amp\Promise;
use Amp\Success;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\RuleViolation;

final class IgnoreBots implements RuleInterface
{
    public function getDetailedMessage(PayloadInterface $payload): string
    {
        $author = $payload->getMessage()->getAuthor();

        return sprintf(
            'Ignoring message from %s bot with user ID [%s]',
            ucfirst(strtolower($payload->getEnvironment()->getName())),
            $author->getId()
        );
    }

    /**
     * @param PayloadInterface $payload
     * @return Failure<RuleViolation>|Promise<PayloadInterface>
     */
    public function enforce(PayloadInterface $payload): Failure|Promise
    {
        if ($payload->getMessage()->getAuthor()->isBot()) {
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
        return RuleInterface::PREFIX_INTERNAL . 'ignore-bots';
    }
}
