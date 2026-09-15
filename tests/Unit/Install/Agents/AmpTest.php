<?php

declare(strict_types=1);

namespace Tests\Unit\Install\Agents;

use Heritage\Support\Facades\File;
use Ugarit\Boost\Install\Agents\Amp;
use Ugarit\Boost\Install\Detection\DetectionStrategyFactory;
use Ugarit\Boost\Install\Enums\McpInstallationStrategy;
use Mockery;

beforeEach(function (): void {
    $this->strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
});

test('name returns amp', function (): void {
    $agent = new Amp($this->strategyFactory);

    expect($agent->name())->toBe('amp');
});

test('displayName returns Amp', function (): void {
    $agent = new Amp($this->strategyFactory);

    expect($agent->displayName())->toBe('Amp');
});

test('mcpInstallationStrategy returns FILE', function (): void {
    $agent = new Amp($this->strategyFactory);

    expect($agent->mcpInstallationStrategy())->toBe(McpInstallationStrategy::FILE);
});

test('guidelinesPath returns AGENTS.md by default', function (): void {
    $agent = new Amp($this->strategyFactory);

    expect($agent->guidelinesPath())->toBe('AGENTS.md');
});

test('skillsPath returns .agents/skills by default', function (): void {
    $agent = new Amp($this->strategyFactory);

    expect($agent->skillsPath())->toBe('.agents/skills');
});

test('projectDetectionConfig only uses .amp directory', function (): void {
    $agent = new Amp($this->strategyFactory);

    expect($agent->projectDetectionConfig())->toBe([
        'paths' => ['.amp'],
    ]);
});

test('installMcp with env vars writes directly to settings file', function (): void {
    $settingsPath = base_path('.amp/settings.json');

    File::shouldReceive('ensureDirectoryExists')
        ->once()
        ->with(dirname($settingsPath));

    File::shouldReceive('exists')
        ->once()
        ->with($settingsPath)
        ->andReturn(false);

    File::shouldReceive('put')
        ->once()
        ->withArgs(function (string $path, string $content) use ($settingsPath): bool {
            if ($path !== $settingsPath) {
                return false;
            }

            $decoded = json_decode($content, true);

            return isset($decoded['amp.mcpServers']['herd'])
                && $decoded['amp.mcpServers']['herd']['command'] === 'herd'
                && $decoded['amp.mcpServers']['herd']['args'][0] === 'php'
                && $decoded['amp.mcpServers']['herd']['args'][1] === '/path/to/mcp'
                && $decoded['amp.mcpServers']['herd']['env']['SITE_PATH'] === '/project';
        })
        ->andReturn(true);

    $agent = new Amp($this->strategyFactory);

    expect($agent->installMcp('herd', 'herd php', ['/path/to/mcp'], ['SITE_PATH' => '/project']))->toBeTrue();
});

test('installMcp without env vars writes directly to settings file', function (): void {
    $settingsPath = base_path('.amp/settings.json');

    File::shouldReceive('ensureDirectoryExists')
        ->once()
        ->with(dirname($settingsPath));

    File::shouldReceive('exists')
        ->once()
        ->with($settingsPath)
        ->andReturn(false);

    File::shouldReceive('put')
        ->once()
        ->withArgs(function (string $path, string $content) use ($settingsPath): bool {
            if ($path !== $settingsPath) {
                return false;
            }

            $decoded = json_decode($content, true);

            return isset($decoded['amp.mcpServers']['ugarit-boost'])
                && $decoded['amp.mcpServers']['ugarit-boost']['command'] === 'php'
                && $decoded['amp.mcpServers']['ugarit-boost']['args'] === ['scribe', 'boost:mcp']
                && ! isset($decoded['amp.mcpServers']['ugarit-boost']['env']);
        })
        ->andReturn(true);

    $agent = new Amp($this->strategyFactory);

    expect($agent->installMcp('ugarit-boost', 'php', ['scribe', 'boost:mcp']))->toBeTrue();
});

test('installHttpMcp writes url config directly to settings file', function (): void {
    $settingsPath = base_path('.amp/settings.json');

    File::shouldReceive('ensureDirectoryExists')
        ->once()
        ->with(dirname($settingsPath));

    File::shouldReceive('exists')
        ->once()
        ->with($settingsPath)
        ->andReturn(false);

    File::shouldReceive('put')
        ->once()
        ->withArgs(function (string $path, string $content) use ($settingsPath): bool {
            if ($path !== $settingsPath) {
                return false;
            }

            $decoded = json_decode($content, true);

            return isset($decoded['amp.mcpServers']['nightwatch'])
                && $decoded['amp.mcpServers']['nightwatch']['url'] === 'https://nightwatch.ugarit.com/mcp';
        })
        ->andReturn(true);

    $agent = new Amp($this->strategyFactory);

    expect($agent->installHttpMcp('nightwatch', 'https://nightwatch.ugarit.com/mcp'))->toBeTrue();
});

test('installMcp overwrites existing amp server config while preserving unrelated settings', function (): void {
    $settingsPath = base_path('.amp/settings.json');
    $existingConfig = json_encode([
        'amp.defaultVisibility' => 'workspace',
        'amp.mcpServers' => [
            'ugarit-boost' => [
                'command' => 'old-php',
                'args' => ['old-scribe', 'old:command'],
            ],
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    File::shouldReceive('ensureDirectoryExists')
        ->once()
        ->with(dirname($settingsPath));

    File::shouldReceive('exists')
        ->once()
        ->with($settingsPath)
        ->andReturn(true);

    File::shouldReceive('get')
        ->once()
        ->with($settingsPath)
        ->andReturn($existingConfig);

    File::shouldReceive('put')
        ->once()
        ->withArgs(function (string $path, string $content) use ($settingsPath): bool {
            if ($path !== $settingsPath) {
                return false;
            }

            $decoded = json_decode($content, true);

            return $decoded['amp.defaultVisibility'] === 'workspace'
                && $decoded['amp.mcpServers']['ugarit-boost']['command'] === 'php'
                && $decoded['amp.mcpServers']['ugarit-boost']['args'] === ['scribe', 'boost:mcp'];
        })
        ->andReturn(true);

    $agent = new Amp($this->strategyFactory);

    expect($agent->installMcp('ugarit-boost', 'php', ['scribe', 'boost:mcp']))->toBeTrue();
});

test('installMcp returns false when existing settings json is invalid', function (): void {
    $settingsPath = base_path('.amp/settings.json');

    File::shouldReceive('ensureDirectoryExists')
        ->once()
        ->with(dirname($settingsPath));

    File::shouldReceive('exists')
        ->once()
        ->with($settingsPath)
        ->andReturn(true);

    File::shouldReceive('get')
        ->once()
        ->with($settingsPath)
        ->andReturn('{invalid json');

    File::shouldReceive('put')->never();

    $agent = new Amp($this->strategyFactory);

    expect($agent->installMcp('ugarit-boost', 'php', ['scribe', 'boost:mcp']))->toBeFalse();
});
