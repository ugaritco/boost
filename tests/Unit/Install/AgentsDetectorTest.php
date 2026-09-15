<?php

declare(strict_types=1);

use Heritage\Container\Container;
use Heritage\Support\Collection;
use Ugarit\Boost\BoostManager;
use Ugarit\Boost\Install\Agents\Agent;
use Ugarit\Boost\Install\Agents\Amp;
use Ugarit\Boost\Install\Agents\Antigravity;
use Ugarit\Boost\Install\Agents\ClaudeCode;
use Ugarit\Boost\Install\Agents\Codex;
use Ugarit\Boost\Install\Agents\Copilot;
use Ugarit\Boost\Install\Agents\Cursor;
use Ugarit\Boost\Install\Agents\Factory;
use Ugarit\Boost\Install\Agents\GrokBuild;
use Ugarit\Boost\Install\Agents\Junie;
use Ugarit\Boost\Install\Agents\Kiro;
use Ugarit\Boost\Install\Agents\OpenCode;
use Ugarit\Boost\Install\Agents\Pi;
use Ugarit\Boost\Install\Agents\Zed;
use Ugarit\Boost\Install\AgentsDetector;
use Ugarit\Boost\Install\Enums\Platform;

beforeEach(function (): void {
    $this->container = new Container;
    $this->boostManager = new BoostManager;
    $this->detector = new AgentsDetector($this->container, $this->boostManager);
});

afterEach(function (): void {
    Mockery::close();
});

it('returns collection of all registered agents', function (): void {
    $agents = $this->detector->getAgents();

    expect($agents)->toBeInstanceOf(Collection::class)
        ->and($agents->count())->toBe(13)
        ->and($agents->keys()->toArray())->toBe([
            'amp', 'antigravity', 'claude_code', 'codex', 'copilot', 'cursor', 'factory', 'grok_build', 'junie', 'kiro', 'opencode', 'pi', 'zed',
        ]);

    $agents->each(function ($agent): void {
        expect($agent)->toBeInstanceOf(Agent::class);
    });
});

it('returns an array of detected agents names for system discovery', function (): void {
    $mockJunie = Mockery::mock(Agent::class);
    $mockJunie->shouldReceive('detectOnSystem')->with(Mockery::type(Platform::class))->andReturn(true);
    $mockJunie->shouldReceive('name')->andReturn('junie');

    $mockCursor = Mockery::mock(Agent::class);
    $mockCursor->shouldReceive('detectOnSystem')->with(Mockery::type(Platform::class))->andReturn(true);
    $mockCursor->shouldReceive('name')->andReturn('cursor');

    $mockOther = Mockery::mock(Agent::class);
    $mockOther->shouldReceive('detectOnSystem')->with(Mockery::type(Platform::class))->andReturn(false);
    $mockOther->shouldReceive('name')->andReturn('other');

    $this->container->bind(Amp::class, fn () => $mockOther);
    $this->container->bind(Junie::class, fn () => $mockJunie);
    $this->container->bind(Cursor::class, fn () => $mockCursor);
    $this->container->bind(ClaudeCode::class, fn () => $mockOther);
    $this->container->bind(Codex::class, fn () => $mockOther);
    $this->container->bind(Copilot::class, fn () => $mockOther);
    $this->container->bind(Factory::class, fn () => $mockOther);
    $this->container->bind(Kiro::class, fn () => $mockOther);
    $this->container->bind(OpenCode::class, fn () => $mockOther);
    $this->container->bind(Antigravity::class, fn () => $mockOther);
    $this->container->bind(Zed::class, fn () => $mockOther);
    $this->container->bind(Pi::class, fn () => $mockOther);
    $this->container->bind(GrokBuild::class, fn () => $mockOther);

    $detector = new AgentsDetector($this->container, $this->boostManager);
    $detected = $detector->discoverSystemInstalledAgents();

    expect($detected)->toBe(['cursor', 'junie']);
});

it('returns an empty array when no agents are detected for system discovery', function (): void {
    $mockAgent = Mockery::mock(Agent::class);
    $mockAgent->shouldReceive('detectOnSystem')->with(Mockery::type(Platform::class))->andReturn(false);
    $mockAgent->shouldReceive('name')->andReturn('mock');

    $this->container->bind(Amp::class, fn () => $mockAgent);
    $this->container->bind(Junie::class, fn () => $mockAgent);
    $this->container->bind(Cursor::class, fn () => $mockAgent);
    $this->container->bind(ClaudeCode::class, fn () => $mockAgent);
    $this->container->bind(Codex::class, fn () => $mockAgent);
    $this->container->bind(Copilot::class, fn () => $mockAgent);
    $this->container->bind(Factory::class, fn () => $mockAgent);
    $this->container->bind(Kiro::class, fn () => $mockAgent);
    $this->container->bind(OpenCode::class, fn () => $mockAgent);
    $this->container->bind(Antigravity::class, fn () => $mockAgent);
    $this->container->bind(Zed::class, fn () => $mockAgent);
    $this->container->bind(Pi::class, fn () => $mockAgent);
    $this->container->bind(GrokBuild::class, fn () => $mockAgent);

    $detector = new AgentsDetector($this->container, $this->boostManager);
    $detected = $detector->discoverSystemInstalledAgents();

    expect($detected)->toBe([]);
});

it('returns an array of detected agent names for project discovery', function (): void {
    $basePath = '/test/project';

    $mockJunie = Mockery::mock(Agent::class);
    $mockJunie->shouldReceive('detectInProject')->with($basePath)->andReturn(false);
    $mockJunie->shouldReceive('name')->andReturn('junie');

    $mockClaudeCode = Mockery::mock(Agent::class);
    $mockClaudeCode->shouldReceive('detectInProject')->with($basePath)->andReturn(true);
    $mockClaudeCode->shouldReceive('name')->andReturn('claude_code');

    $mockOther = Mockery::mock(Agent::class);
    $mockOther->shouldReceive('detectInProject')->with($basePath)->andReturn(false);
    $mockOther->shouldReceive('name')->andReturn('other');

    $this->container->bind(Amp::class, fn () => $mockOther);
    $this->container->bind(Junie::class, fn () => $mockJunie);
    $this->container->bind(Cursor::class, fn () => $mockOther);
    $this->container->bind(ClaudeCode::class, fn () => $mockClaudeCode);
    $this->container->bind(Codex::class, fn () => $mockOther);
    $this->container->bind(Copilot::class, fn () => $mockOther);
    $this->container->bind(Factory::class, fn () => $mockOther);
    $this->container->bind(Kiro::class, fn () => $mockOther);
    $this->container->bind(OpenCode::class, fn () => $mockOther);
    $this->container->bind(Antigravity::class, fn () => $mockOther);
    $this->container->bind(Zed::class, fn () => $mockOther);
    $this->container->bind(Pi::class, fn () => $mockOther);
    $this->container->bind(GrokBuild::class, fn () => $mockOther);

    $detector = new AgentsDetector($this->container, $this->boostManager);
    $detected = $detector->discoverProjectInstalledAgents($basePath);

    expect($detected)->toBe(['claude_code']);
});

it('returns an empty array when no agents are detected for project discovery', function (): void {
    $basePath = '/empty/project';

    $mockAgent = Mockery::mock(Agent::class);
    $mockAgent->shouldReceive('detectInProject')->with($basePath)->andReturn(false);
    $mockAgent->shouldReceive('name')->andReturn('mock');

    $this->container->bind(Amp::class, fn () => $mockAgent);
    $this->container->bind(Junie::class, fn () => $mockAgent);
    $this->container->bind(Cursor::class, fn () => $mockAgent);
    $this->container->bind(ClaudeCode::class, fn () => $mockAgent);
    $this->container->bind(Codex::class, fn () => $mockAgent);
    $this->container->bind(Copilot::class, fn () => $mockAgent);
    $this->container->bind(Factory::class, fn () => $mockAgent);
    $this->container->bind(Kiro::class, fn () => $mockAgent);
    $this->container->bind(OpenCode::class, fn () => $mockAgent);
    $this->container->bind(Antigravity::class, fn () => $mockAgent);
    $this->container->bind(Zed::class, fn () => $mockAgent);
    $this->container->bind(Pi::class, fn () => $mockAgent);
    $this->container->bind(GrokBuild::class, fn () => $mockAgent);

    $detector = new AgentsDetector($this->container, $this->boostManager);
    $detected = $detector->discoverProjectInstalledAgents($basePath);

    expect($detected)->toBe([]);
});
