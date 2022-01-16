<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Message\Processor;

use Amp\Promise;
use Bakabot\Chat\Message\Message;
use Bakabot\Command\Prefix\Prefix;

final class CommandParser extends AbstractProcessor
{
    private Prefix $prefix;

    public function __construct(Prefix $prefix)
    {
        $this->prefix = $prefix;
    }

    private function normalizeMessage(string $message): string
    {
        $normalizedMessage = preg_replace('#\s{2,}#', ' ', $message);

        return substr($normalizedMessage, $this->prefix->getLength());
    }

    /** @return string[] */
    private function parseArguments(string $messageWithoutPrefix): array
    {
        $arguments = str_getcsv($messageWithoutPrefix, ' ');
        $arguments = array_map('trim', array_filter($arguments));

        return array_values(array_filter($arguments));
    }

    /**
     * Passes along the payload (and is able to return a decorated one).
     *
     * @param Message $message
     * @return Promise<Message>
     */
    public function process(Message $message): Promise
    {
        $contents = $message->content;

        if ($this->prefix->matches($contents) === false) {
            return $this->message($message); // continue with original payload
        }

        $messageWithoutPrefix = $this->normalizeMessage($contents);
        $arguments = $this->parseArguments($messageWithoutPrefix);

        $name = array_shift($arguments);
        $rawArguments = trim(preg_replace("#^$name#", '', $messageWithoutPrefix));

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
