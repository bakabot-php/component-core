<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall\Rule;

use Amp\Promise;
use Bakabot\Chat\Channel\PrivateChannel;
use Bakabot\Chat\Message\Message;
use Bakabot\Processor\Firewall\RuleViolation;

final class IgnoreDirectMessages extends FirewallRule
{
    public function detailedMessage(Message $message): string
    {
        return 'Ignoring direct message';
    }

    /**
     * @param Message $message
     * @return Promise<?RuleViolation>
     */
    public function handle(Message $message): Promise
    {
        if ($message->channel instanceof PrivateChannel) {
            return $this->fail();
        }

        return parent::handle($message);
    }

    public function name(): string
    {
        return self::PREFIX_INTERNAL . 'ignore-direct-messages';
    }
}
