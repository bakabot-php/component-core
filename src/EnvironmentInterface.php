<?php

declare(strict_types = 1);

namespace Bakabot;

use Bakabot\Chat\User\UserInterface;
use Bakabot\Command\Collection as CommandCollection;
use Bakabot\Command\HelpCommand;

interface EnvironmentInterface
{
    public function formatMention(UserInterface|string $identity): string;

    public function getActiveUser(): UserInterface;

    public function getHelpCommand(CommandCollection $commands, string $requestedCommand): HelpCommand;

    public function getName(): string;

    public function parseMention(string $mention): ?string;
}
