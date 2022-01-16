<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Amp\Promise;
use Bakabot\Chat\Message\Message;

interface SettingsSource
{
    /**
     * @param Message $message
     * @return Promise<Settings>
     */
    public function settings(Message $message): Promise;
}
