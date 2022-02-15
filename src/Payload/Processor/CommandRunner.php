<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Deferred;
use Amp\Promise;
use Amp\Success;
use Bakabot\Action\ActionInterface;
use Bakabot\Action\DoNothing;
use Bakabot\Command\AbstractCommand;
use Bakabot\Command\Collection;
use Bakabot\Command\HelpCommand;
use Bakabot\Command\Payload as CommandPayload;
use Bakabot\Payload\PayloadInterface;

use function Amp\call;

final class CommandRunner extends AbstractProcessor
{
    private Collection $commands;

    public function __construct(Collection $commands)
    {
        $this->commands = $commands;
    }

    /**
     * Passes along the payload (and is able to return a decorated one).
     * Will eventually resolve to an @see ActionInterface.
     *
     * @param PayloadInterface $payload
     * @return Promise<PayloadInterface|ActionInterface>
     */
    public function process(PayloadInterface $payload): Promise
    {
        return call(function () use ($payload) {
            // can only execute command payloads
            if (!($payload instanceof CommandPayload)) {
                return new Success($payload);
            }

            $requestedCommand = $payload->getCommandName();
            $command = $this->commands->findByName($requestedCommand);

            if ($command === null) {
                return new Success(new DoNothing());
            }

            if ($command instanceof AbstractCommand) {
                $command->bind($payload);
            }

            return $command->run();
        });
    }
}
