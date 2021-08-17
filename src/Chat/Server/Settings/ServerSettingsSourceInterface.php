<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Amp\Promise;
use Bakabot\Payload\PayloadInterface;

interface ServerSettingsSourceInterface
{
    /**
     * @param PayloadInterface $payload
     * @return Promise<ServerSettings>
     */
    public function getServerSettings(PayloadInterface $payload): Promise;
}
