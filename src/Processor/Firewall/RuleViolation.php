<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall;

use Bakabot\Action\Action;
use RuntimeException;

final class RuleViolation extends RuntimeException
{
    private Action $action;
    private Rule $rule;

    public function __construct(Rule $rule, ?Action $action = null)
    {
        parent::__construct(sprintf('Failed firewall rule [%s]', $rule->name()));

        $this->action = $action;
        $this->rule = $rule;
    }

    public function action(): Action
    {
        return $this->action;
    }

    public function rule(): Rule
    {
        return $this->rule;
    }
}
