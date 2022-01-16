<?php

declare(strict_types = 1);

namespace Bakabot\Chat\Server\Settings;

use Amp\Promise;
use Bakabot\Chat\Server\Language\Language;
use Bakabot\Chat\Server\Server;
use Bakabot\Command\Prefix\Prefix;
use Bakabot\Environment;
use Bakabot\Payload\PayloadInterface;
use PHPUnit\Framework\TestCase;

class SettingsIntegrationTest extends TestCase
{
    /** @test */
    public function json_database_source_non_server_payload_generates_defaults(): void
    {
        $language = new Language('en');
        $prefix = new Prefix('!');

        $settingsSource = new JsonFileSource('/tmp/database.json', $language, $prefix);

        $payload = $this->createMock(PayloadInterface::class);
        $settings = Promise\wait($settingsSource->settings($payload));

        self::assertEquals(new AllowedCommands([]), $settings->getAllowedCommands());
        self::assertEquals($language, $settings->getLanguage());
        self::assertEquals($prefix, $settings->getPrefix());
    }

    /** @test */
    public function json_database_source_handles_defaults(): void
    {
        $payload = $this->createMock(PayloadInterface::class);
        $payload->method('getServer')->willReturn(new Server('1', 'test'));

        $environment = $this->createMock(Environment::class);
        $environment->method('getName')->willReturn('test');

        $payload->method('getEnvironment')->willReturn($environment);

        $language = new Language('en');
        $prefix = new Prefix('!');

        $settingsSource = new JsonFileSource('/tmp/database.json', $language, $prefix);
        $settings = Promise\wait($settingsSource->settings($payload));

        self::assertEquals(Settings::withDefaults($language, $prefix), $settings);
    }

    /** @test */
    public function table_gateway_source_reflects_row(): void
    {
        file_put_contents(
            '/tmp/database.json',
            json_encode(
                [
                    'test_1' => [
                        'allowed_commands' => ['test'],
                        'language' => 'de',
                        'prefix' => '#!',
                    ]
                ]
            )
        );

        $payload = $this->createMock(PayloadInterface::class);
        $payload->method('getServer')->willReturn(new Server('1', 'test'));

        $environment = $this->createMock(Environment::class);
        $environment->method('getName')->willReturn('test');

        $payload->method('getEnvironment')->willReturn($environment);

        $settingsSource = new JsonFileSource('/tmp/database.json', new Language('en'), new Prefix('!'));
        Promise\wait($settingsSource->settings($payload));
        $settings = Promise\wait($settingsSource->settings($payload));

        self::assertEquals(new AllowedCommands(['test']), $settings->getAllowedCommands());
        self::assertEquals(new Language('de'), $settings->getLanguage());
        self::assertEquals(new Prefix('#!'), $settings->getPrefix());

        @unlink('/tmp/database.json');
    }
}
