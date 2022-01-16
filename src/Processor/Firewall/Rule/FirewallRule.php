<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall\Rule;

use Amp\Failure;
use Bakabot\Action\Action;
use Bakabot\Action\DoNothing;
use Bakabot\Component\Core\Common\Cloneable;
use Bakabot\Processor\BaseHandler;
use Bakabot\Processor\Firewall\Rule;
use Bakabot\Processor\Firewall\RuleViolation;

abstract class FirewallRule extends BaseHandler implements Rule
{
    use Cloneable;

    private ?Action $action = null;

    final protected function fail(?Action $action = null): Failure
    {
        return new Failure(
            new RuleViolation(
                $this,
                $action ?? $this->action ?? new DoNothing(removesTriggerMessage: false)
            )
        );
    }

    final protected function reject(): Failure
    {
        return $this->fail(new DoNothing(removesTriggerMessage: true));
    }

    final public function withAction(Action $action): static
    {
        return $this->copy(action: $action);
    }
}
