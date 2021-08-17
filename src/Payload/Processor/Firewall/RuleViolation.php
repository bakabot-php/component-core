<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor\Firewall;

use Bakabot\Payload\Processor\Firewall\Rule\RuleInterface;
use RuntimeException;

final class RuleViolation extends RuntimeException
{
    private RuleInterface $rule;

    public function __construct(RuleInterface $rule)
    {
        parent::__construct(sprintf('Failed firewall rule [%s]', $rule->getName()));

        $this->rule = $rule;
    }

    public function getRule(): RuleInterface
    {
        return $this->rule;
    }
}
