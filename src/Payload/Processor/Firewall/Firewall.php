<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall;

use Amp\Deferred;
use Amp\Promise;
use Amp\Success;
use Bakabot\Action;
use Bakabot\Payload\PayloadInterface;
use Bakabot\Payload\Processor\Firewall\Rule;
use Bakabot\Payload\Processor\ProcessorInterface;
use Psr\Log\LoggerInterface;
use Throwable;

final class Firewall implements ProcessorInterface
{
    private LoggerInterface $logger;
    /** @var Rule\RuleInterface[] */
    private array $rules = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function addRule(Rule\RuleInterface $rule): void
    {
        $this->rules[$rule->getName()] = $rule;
    }

    /**
     * @param PayloadInterface $payload
     * @return Promise<PayloadInterface|Action\ActionInterface>
     * @throws Throwable
     */
    public function process(PayloadInterface $payload): Promise
    {
        // If no rules are registered we can return early,
        // since no manipulation of the payload will occur
        if (count($this->rules) === 0) {
            return new Success($payload);
        }

        $action = null;
        $currentPayload = $payload;
        $deferred = new Deferred();

        foreach ($this->rules as $rule) {
            /** @var PayloadInterface $currentPayload */
            $promise = $rule->enforce($currentPayload);
            $promise->onResolve(
                function (?Throwable $violation, ?PayloadInterface $payload) use (
                    &$action,
                    &$currentPayload,
                    $rule
                ): void {
                    if ($violation instanceof RuleViolation) {
                        $context = [
                            'rule' => $violation->getRule(),
                        ];

                        $this->logger->debug($violation->getMessage(), $context);

                        /** @var PayloadInterface $currentPayload */
                        $detailedMessage = $rule->getDetailedMessage($currentPayload);
                        if ($detailedMessage !== null) {
                            $this->logger->debug($detailedMessage, $context);
                        }

                        $this->logger->notice('Skipped due to firewall rule [' . $rule->getName() . ']', $context);

                        $action = new Action\DoNothing();
                        return;
                    }

                    $currentPayload = $payload;
                }
            );
        }

        $deferred->resolve($action ?? $currentPayload);

        /** @var Promise<PayloadInterface|Action\ActionInterface> $deferredPromise */
        $deferredPromise = $deferred->promise();

        return $deferredPromise;
    }
}
