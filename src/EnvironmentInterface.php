<?php

declare(strict_types = 1);

namespace Bakabot;

use Bakabot\Chat\User\UserInterface;
use Bakabot\Command\Collection as CommandCollection;
use Bakabot\Command\HelpCommandInterface;

interface EnvironmentInterface
{
    public function formatPing(UserInterface|string $identity): string;

    public function getActiveUser(): UserInterface;

    public function getHelpCommand(CommandCollection $commands, string $requestedCommand): HelpCommandInterface;

    public function getName(): string;

    public function parsePing(string $ping): ?string;
}
