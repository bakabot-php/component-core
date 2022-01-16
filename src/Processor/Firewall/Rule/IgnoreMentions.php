<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall\Rule;

use Amp\Promise;
use Bakabot\Chat\Message\Message;
use Bakabot\Processor\Firewall\RuleViolation;

final class IgnoreMentions extends FirewallRule
{
    public function detailedMessage(Message $message): string
    {
        return 'Ignoring message which does not mention the bot';
    }

    /**
     * @param Message $message
     * @return Promise<?RuleViolation>
     */
    public function handle(Message $message): Promise
    {
        $mentions = $message->mentions();

        if (
            $mentions->count() === 0
            || $mentions->users()->has($message->environment->user())
        ) {
            return parent::handle($message);
        }

        return $this->fail();
    }

    public function name(): string
    {
        return self::PREFIX_INTERNAL . 'ignore-mentions';
    }
}
