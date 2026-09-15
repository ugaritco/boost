<?php

declare(strict_types=1);

use Heritage\Support\Facades\Scribe;
use Ugarit\Boost\Mcp\ToolRegistry;
use Ugarit\Boost\Mcp\Tools\DatabaseConnections;
use Ugarit\Boost\Mcp\Tools\DatabaseQuery;
use Symfony\Component\Console\Command\Command;
use Tests\Fixtures\ThrowingTool;

beforeEach(function (): void {
    ToolRegistry::clearCache();
    config()->set('app.name', 'TestApp');
    config()->set('boost.mcp.tools.exclude', []);
});

afterEach(function (): void {
    config()->set('boost.mcp.tools.exclude', []);
    ToolRegistry::clearCache();
});

it('exits with error when the tool class is not in the registry', function (): void {
    $this->scribe('boost:execute-tool', [
        'tool' => 'App\\Fake\\NonExistentTool',
        'arguments' => base64_encode('{}'),
    ])->assertExitCode(Command::FAILURE)
        ->expectsOutputToContain('Tool not registered or not allowed');
});

it('exits with error when the tool is in the exclude config', function (): void {
    config()->set('boost.mcp.tools.exclude', [DatabaseConnections::class]);
    ToolRegistry::clearCache();

    $this->scribe('boost:execute-tool', [
        'tool' => DatabaseConnections::class,
        'arguments' => base64_encode('{}'),
    ])->assertExitCode(Command::FAILURE)
        ->expectsOutputToContain('Tool not registered or not allowed');
});

it('exits with error when decoded arguments contain invalid JSON', function (): void {
    $this->scribe('boost:execute-tool', [
        'tool' => DatabaseConnections::class,
        'arguments' => base64_encode('{not valid json'),
    ])->assertExitCode(Command::FAILURE)
        ->expectsOutputToContain('Invalid arguments format');
});

it('outputs JSON with isError false on successful tool execution', function (): void {
    ob_start();
    $exitCode = Scribe::call('boost:execute-tool', [
        'tool' => DatabaseConnections::class,
        'arguments' => base64_encode('{}'),
    ]);
    $rawOutput = ob_get_clean();

    $json = json_decode($rawOutput, true);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($json)->toHaveKeys(['isError', 'content'])
        ->and($json['isError'])->toBeFalse();
});

it('outputs JSON with isError true when the tool returns an error response', function (): void {
    ob_start();
    Scribe::call('boost:execute-tool', [
        'tool' => DatabaseQuery::class,
        'arguments' => base64_encode(json_encode(['query' => 'DELETE FROM users'])),
    ]);
    $rawOutput = ob_get_clean();

    $json = json_decode($rawOutput, true);

    expect($json['isError'])->toBeTrue();
});

it('catches tool exceptions and outputs error JSON with failure exit code', function (): void {
    config()->set('boost.mcp.tools.include', [ThrowingTool::class]);
    ToolRegistry::clearCache();

    $this->scribe('boost:execute-tool', [
        'tool' => ThrowingTool::class,
        'arguments' => base64_encode('{}'),
    ])->assertExitCode(Command::FAILURE)
        ->expectsOutputToContain('Intentional test exception');
});
