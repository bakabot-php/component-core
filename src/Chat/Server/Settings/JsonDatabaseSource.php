<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Amp\ByteStream\Payload;
use Amp\ByteStream\ResourceInputStream;
use Amp\Promise;
use Amp\Success;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Language\LanguageSourceInterface;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Prefix\PrefixSourceInterface;
use Bakabot\Payload\PayloadInterface;
use function Amp\call;

final class JsonDatabaseSource implements ServerSettingsSourceInterface
{
    /** @var array<string, array<string, mixed>>|null */
    private ?array $cache = null;
    private string $file;
    private LanguageSourceInterface $languageSource;
    private PrefixSourceInterface $prefixSource;

    public function __construct(
        string $file,
        LanguageSourceInterface $languageSource,
        PrefixSourceInterface $prefixSource
    ) {
        $this->file = $file;
        $this->languageSource = $languageSource;
        $this->prefixSource = $prefixSource;
    }

    /**
     * @param array<string, mixed> $settings
     * @return ServerSettings
     */
    private function create(array $settings): ServerSettings
    {
        if ($settings === []) {
            return ServerSettings::withDefaults($this->languageSource, $this->prefixSource);
        }

        /** @var string[] $allowedCommands */
        $allowedCommands = $settings['allowed_commands'] ?? [];
        /** @var array<array-key, string> $channelAllowList */
        $channelAllowList = $settings['channel_allowlist'] ?? [];
        /** @var array<array-key, string> $channelDenyList */
        $channelDenyList = $settings['channel_denylist'] ?? [];

        return new ServerSettings(
            new Language((string) ($settings['language'] ?? (string) $this->languageSource->getLanguage())),
            new Prefix((string) ($settings['prefix'] ?? (string) $this->prefixSource->getPrefix())),
            new AllowedCommands($allowedCommands),
            new ChannelList($channelAllowList),
            new ChannelList($channelDenyList),
        );
    }

    /**
     * @return Promise<array<string, array<string, mixed>>>
     */
    private function load(): Promise
    {
        return call(function () {
            if ($this->cache !== null) {
                return new Success($this->cache);
            }

            if (($stream = @fopen($this->file, 'r')) === false) {
                return new Success($this->cache = []);
            }

            $stream = new ResourceInputStream($stream);

            try {
                $file = new Payload($stream);
                $contents = yield $file->buffer();

                /** @var array<string, array<string, mixed>> $serverSettings */
                $serverSettings = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

                return new Success($this->cache = $serverSettings);
            } finally {
                $stream->close();
            }
        });
    }

    /**
     * @param PayloadInterface $payload
     * @return Promise<ServerSettings>
     */
    public function getServerSettings(PayloadInterface $payload): Promise
    {
        return call(function () use ($payload) {
            $server = $payload->getServer();

            if ($server === null) {
                return new Success($this->create([]));
            }

            $serverSettings = yield $this->load();
            $key = sprintf('%s_%s', $payload->getEnvironment()->getName(), $server->getId());

            return new Success($this->create($serverSettings[$key] ?? []));
        });
    }
}
