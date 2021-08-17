<?php

declare(strict_types = 1);

namespace Bakabot\Payload\Processor;

use Amp\Promise;
use Bakabot\Action\DoNothing;
use Bakabot\Chat\Channel\ChannelInterface;
use Bakabot\Chat\Message\IncomingMessage;
use Bakabot\Chat\User\User;
use Bakabot\Component\CoreComponent;
use Bakabot\EnvironmentInterface;
use Bakabot\Payload\Payload;
use DateTimeImmutable;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;

/**  */
class ProcessorChainIntegrationTest extends TestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        putenv('APP_DEBUG=true');
        putenv('APP_DIR=/tmp');
        putenv('APP_ENV=test');

        $containerBuilder = new ContainerBuilder();
        (new CoreComponent())->register($containerBuilder);

        $this->container = $containerBuilder->build();
    }

    private function getConfiguredProcessorChain(): ProcessorChain
    {
        return $this->container->get(ProcessorChain::class);
    }

    private function getLogger(): TestLogger
    {
        return $this->container->get(LoggerInterface::class);
    }

    /** @test */
    public function preconfigured_chain_firewalls_bots(): void
    {
        $environment = $this->createMock(EnvironmentInterface::class);
        $environment->method('getName')->willReturn('test');

        $payload = new Payload(
            $environment,
            $channel = $this->createMock(ChannelInterface::class),
            new IncomingMessage(
                '1',
                'Beep boop',
                $channel,
                new User('1', 'Bakabot', null, true),
                new DateTimeImmutable(),
                null
            ),
            null
        );

        $chain = $this->getConfiguredProcessorChain();
        $action = Promise\wait($chain->process($payload));

        self::assertInstanceOf(DoNothing::class, $action);
        self::assertTrue($this->getLogger()->hasDebug('Failed firewall rule [internal:ignore-bots]'));
        self::assertTrue($this->getLogger()->hasDebug('Ignoring message from Test bot with user ID [1]'));
        self::assertTrue($this->getLogger()->hasNotice('Skipped due to firewall rule [internal:ignore-bots]'));
    }
}
