<?php

declare(strict_types = 1);

namespace Bakabot\Processor\Firewall;

use Amp\Promise;
use Bakabot\Action\Action;
use Bakabot\Chat\Message\Message;
use Bakabot\Processor\BaseHandler;
use Bakabot\Processor\HandlerChain;
use Psr\Log\LoggerInterface;
use Throwable;

final class Firewall extends BaseHandler
{
    use HandlerChain;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Message $message
     * @return Promise<?Action>
     */
    public function handle(Message $message): Promise
    {
        $promise = parent::handle($message);
        $promise->onResolve(
            function (?Throwable $violation) use ($message) {
                if ($violation instanceof RuleViolation) {
                    $this->logger->debug($violation->getMessage());

                    $detailedMessage = $violation->rule()->detailedMessage($message);
                    if ($detailedMessage !== null) {
                        $this->logger->debug($detailedMessage);
                    }

                    $this->logger->info('Skipped due to firewall rule [' . $violation->rule()->name() . ']');

                    return $violation->action();
                }
            }
        );

        return $promise;
    }
}
