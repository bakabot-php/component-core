<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall\Rule;

use Amp\Promise;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\User\Bot;
use Bakabot\Processor\Firewall\RuleViolation;

final class IgnoreBots extends FirewallRule
{
    public function detailedMessage(Message $message): string
    {
        return sprintf(
            'Ignoring message from %s bot with user ID [%s]',
            ucfirst(strtolower($message->environment->name())),
            $message->author
        );
    }

    /**
     * @param Message $message
     * @return Promise<?RuleViolation>
     */
    public function handle(Message $message): Promise
    {
        if ($message->author instanceof Bot) {
            return $this->fail();
        }

        return parent::handle($message);
    }

    public function name(): string
    {
        return self::PREFIX_INTERNAL . 'ignore-bots';
    }
}
