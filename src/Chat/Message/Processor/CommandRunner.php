<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Deferred;
use Amp\Promise;
use Bakabot\Action\AbstractAction;
use Bakabot\Command\AbstractCommand;
use Bakabot\Command\Commands;
use Bakabot\Command\HelpCommand;
use Bakabot\Command\BasePayload as CommandPayload;
use Bakabot\Payload\BasePayload;

final class CommandRunner extends AbstractProcessor
{
    private Commands $commands;

    public function __construct(Commands $commands)
    {
        $this->commands = $commands;
    }

    /**
     * Passes along the payload (and is able to return a decorated one).
     * Will eventually resolve to an @param BasePayload $payload
     *
     * @return Promise<BasePayload|AbstractAction>
     *@see Action.
     *
     */
    public function process(BasePayload $payload): Promise
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
                HelpCommand::commandListing($this->commands, $payload, $requestedCommand),
                $deferred
            );
        }

        if ($command instanceof AbstractCommand) {
            $command->bind($payload);
        }

        return $this->promise($command->run(), $deferred);
    }
}
