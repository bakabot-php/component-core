<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action\ActionInterface;
use Bakabot\Command\AbstractCommand;
use Bakabot\Command\Collection;
use Bakabot\Command\HelpCommand;
use Bakabot\Command\Payload as CommandPayload;
use Bakabot\Payload\PayloadInterface;

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
        // can only execute command payloads
        if (!($payload instanceof CommandPayload)) {
            return $this->payload($payload);
        }

        $deferred = new Deferred();

        $requestedCommand = $payload->getCommandName();
        $command = $this->commands->findByName($requestedCommand);

        if ($command === null) {
            return $this->promise(
                HelpCommand::createCommandOverview($this->commands, $payload, $requestedCommand),
                $deferred
            );
        }

        if ($command instanceof AbstractCommand) {
            $command->bind($payload);
        }

        return $this->promise($command->run(), $deferred);
    }
}
