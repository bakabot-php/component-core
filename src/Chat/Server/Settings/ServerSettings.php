<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Language\LanguageSourceInterface;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Prefix\PrefixSourceInterface;

final class ServerSettings
{
    private AllowedCommands $allowedCommands;
    private ChannelList $allowList;
    private ChannelList $denyList;
    private Language $language;
    private Prefix $prefix;

    /**
     * @param Language $language
     * @param Prefix $prefix
     * @param AllowedCommands $allowedCommands
     * @param ChannelList $allowList
     * @param ChannelList $denyList
     */
    public function __construct(
        Language $language,
        Prefix $prefix,
        AllowedCommands $allowedCommands,
        ChannelList $allowList,
        ChannelList $denyList,
    ) {
        $this->allowedCommands = $allowedCommands;
        $this->allowList = $allowList;
        $this->denyList = $denyList;
        $this->language = $language;
        $this->prefix = $prefix;
    }

    public static function withDefaults(
        LanguageSourceInterface $languageSource,
        PrefixSourceInterface $prefixSource
    ): self {
        return new self(
            $languageSource->getLanguage(),
            $prefixSource->getPrefix(),
            new AllowedCommands(),
            new ChannelList(),
            new ChannelList()
        );
    }

    public function getAllowedCommands(): AllowedCommands
    {
        return $this->allowedCommands;
    }

    public function getAllowList(): ChannelList
    {
        return $this->allowList;
    }

    public function getDenyList(): ChannelList
    {
        return $this->denyList;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getPrefix(): Prefix
    {
        return $this->prefix;
    }
}
