<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Parser;

use Amp\Promise;
use Bakabot\Chat\Message\Message;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Trigger\PrefixedMessage;
use Bakabot\Processor\BaseHandler;

final class PrefixParser extends BaseHandler
{
    public function __construct(
        private /* readonly */ Prefix $prefix
    ) {

    }

    private function normalizeMessage(string $message): string
    {
        $normalizedMessage = preg_replace('#\s{2,}#', ' ', $message);

        return substr($normalizedMessage, $this->prefix->getLength());
    }

    /**
     * @return string[]
     */
    private function parseArguments(string $messageWithoutPrefix): array
    {
        $arguments = str_getcsv($messageWithoutPrefix, ' ');
        $arguments = array_map('trim', array_filter($arguments));

        return array_values(array_filter($arguments));
    }

    public function handle(Message $message): Promise
    {
        $content = $message->content;

        if (!$this->prefix->matches($content)) {
            return parent::handle($message);
        }

        $trigger = new PrefixedMessage($this->prefix);

        $normalizedMessage = $this->normalizeMessage($content);
        $arguments = $this->parseArguments($normalizedMessage);

        $name = array_shift($arguments);
        $rawArguments = trim(preg_replace("#^$name#", '', $normalizedMessage));

        // continue with command payload
        $commandPayload = new CommandPayload(
            $payload,
            (string) $this->prefix,
            $name,
            $arguments,
            $rawArguments
        );

        return $this->message($commandPayload);
    }
}
