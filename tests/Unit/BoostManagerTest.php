<?php

declare(strict_types=1);

use Ugarit\Boost\BoostManager;
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
use Tests\Unit\Install\ExampleAgent;

it('returns default agents', function (): void {
    $manager = new BoostManager;
    $registered = $manager->getAgents();

    expect($registered)->toMatchArray([
        'amp' => Amp::class,
        'junie' => Junie::class,
        'cursor' => Cursor::class,
        'claude_code' => ClaudeCode::class,
        'codex' => Codex::class,
        'copilot' => Copilot::class,
        'factory' => Factory::class,
        'kiro' => Kiro::class,
        'opencode' => OpenCode::class,
        'antigravity' => Antigravity::class,
        'zed' => Zed::class,
        'pi' => Pi::class,
        'grok_build' => GrokBuild::class,
    ]);
});

it('returns agents sorted alphabetically by key', function (): void {
    $manager = new BoostManager;
    $manager->registerAgent('boostbot', ExampleAgent::class);

    expect(array_keys($manager->getAgents()))->toBe([
        'amp', 'antigravity', 'boostbot', 'claude_code', 'codex', 'copilot', 'cursor', 'factory', 'grok_build', 'junie', 'kiro', 'opencode', 'pi', 'zed',
    ]);
});

it('can register a single agent', function (): void {
    $manager = new BoostManager;
    $manager->registerAgent('example', ExampleAgent::class);

    $registered = $manager->getAgents();

    expect($registered)->toHaveKey('example')
        ->and($registered['example'])->toBe(ExampleAgent::class)
        ->and($registered)->toHaveKey('junie');
});

it('can register multiple agents', function (): void {
    $manager = new BoostManager;
    $manager->registerAgent('example1', ExampleAgent::class);
    $manager->registerAgent('example2', ExampleAgent::class);

    $registered = $manager->getAgents();

    expect($registered)->toHaveKey('example1')->toHaveKey('example2')
        ->and($registered['example1'])->toBe(ExampleAgent::class)
        ->and($registered['example2'])->toBe(ExampleAgent::class)
        ->and($registered)->toHaveKey('junie');
});

it('throws an exception when registering a duplicate key', function (): void {
    $manager = new BoostManager;

    expect(fn () => $manager->registerAgent('junie', ExampleAgent::class))
        ->toThrow(InvalidArgumentException::class, "Agent 'junie' is already registered");
});

it('throws an exception when registering a custom agent with a duplicate key', function (): void {
    $manager = new BoostManager;
    $manager->registerAgent('custom', ExampleAgent::class);

    expect(fn () => $manager->registerAgent('custom', ExampleAgent::class))
        ->toThrow(InvalidArgumentException::class, "Agent 'custom' is already registered");
});
