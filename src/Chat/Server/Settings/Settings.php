<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Language\LanguageSource;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Command\Prefix\PrefixSource;

final class Settings
{
    public function __construct(
        public /* readonly */ Language $language,
        public /* readonly */ Prefix $prefix,
        public /* readonly */ AllowedCommands $allowedCommands,
        public /* readonly */ ChannelList $allowList,
        public /* readonly */ ChannelList $denyList,
    ) {
    }

    public static function withDefaults(LanguageSource $languageSource, PrefixSource $prefixSource): self
    {
        return new self(
            $languageSource->language(),
            $prefixSource->getPrefix(),
            new AllowedCommands(),
            allowList: new ChannelList(),
            denyList: new ChannelList()
        );
    }
}
