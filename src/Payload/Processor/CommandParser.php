<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Promise;
use Bakabot\Command\Payload as CommandPayload;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Payload\PayloadInterface;

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
     * @param PayloadInterface $payload
     * @return Promise<PayloadInterface>
     */
    public function process(PayloadInterface $payload): Promise
    {
        $message = $payload->getMessage()->getContent();

        if ($this->prefix->matches($message) === false) {
            return $this->payload($payload); // continue with original payload
        }

        $messageWithoutPrefix = $this->normalizeMessage($message);
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

        return $this->payload($commandPayload);
    }
}
