<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Amp\File\FilesystemException;
use Amp\Promise;
use Amp\Success;
use Bakabot\Chat\Channel\ServerChannel;
use Bakabot\Chat\Message\Message;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Language\LanguageSource;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Prefix\PrefixSource;
use function Amp\call;
use function Amp\File\read;

final class JsonFileSource implements SettingsSource
{
    /** @var array<string, array<string, mixed>>|null */
    private ?array $cache = null;
    private string $file;
    private LanguageSource $languageSource;
    private PrefixSource $prefixSource;

    public function __construct(
        string $file,
        LanguageSource $languageSource,
        PrefixSource $prefixSource
    ) {
        $this->file = $file;
        $this->languageSource = $languageSource;
        $this->prefixSource = $prefixSource;
    }

    /**
     * @param array<string, mixed> $settings
     * @return Settings
     */
    private function create(array $settings): Settings
    {
        if ($settings === []) {
            return Settings::withDefaults($this->languageSource, $this->prefixSource);
        }

        /** @var string[] $allowedCommands */
        $allowedCommands = $settings['allowed_commands'] ?? [];
        /** @var array<array-key, string> $channelAllowList */
        $channelAllowList = $settings['channel_allowlist'] ?? [];
        /** @var array<array-key, string> $channelDenyList */
        $channelDenyList = $settings['channel_denylist'] ?? [];

        return new Settings(
            new Language((string) ($settings['language'] ?? $this->languageSource)),
            new Prefix((string) ($settings['prefix'] ?? $this->prefixSource)),
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
        if ($this->cache !== null) {
            return new Success($this->cache);
        }

        return call(function () {
            try {
                /** @var array<string, array<string, mixed>> $serverSettings */
                $serverSettings = json_decode(yield read($this->file), true, 512, JSON_THROW_ON_ERROR);

                return new Success($this->cache = $serverSettings);
            } catch (FilesystemException $ex) {
                if (str_starts_with($ex->getMessage(), 'Failed to read')) {
                    return new Success($this->cache = []);
                }

                throw $ex;
            }
        });
    }

    /**
     * @param Message $message
     * @return Promise<Settings>
     */
    public function settings(Message $message): Promise
    {
        return call(function () use ($message) {
            $channel = $message->channel;

            if (!($channel instanceof ServerChannel)) {
                return new Success($this->create([]));
            }

            $serverSettings = yield $this->load();
            $key = sprintf('%s_%s', $message->environment->name(), $channel->server()->id());

            return new Success($this->create($serverSettings[$key] ?? []));
        });
    }
}
