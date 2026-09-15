<?php

declare(strict_types=1);

use Heritage\Support\Facades\Scribe;
use Ugarit\Boost\Console\StartCommand;
use Symfony\Component\Console\Command\Command;

it('invokes mcp:start with ugarit-boost as the server name', function (): void {
    $mockScribe = Mockery::mock();
    $mockScribe->shouldReceive('call')
        ->once()
        ->with('mcp:start ugarit-boost')
        ->andReturn(0);

    Scribe::swap($mockScribe);

    $command = new StartCommand;

    expect($command->handle())->toBe(Command::SUCCESS);
});

it('returns the same exit code that mcp:start returns', function (): void {
    $mockScribe = Mockery::mock();
    $mockScribe->shouldReceive('call')
        ->once()
        ->with('mcp:start ugarit-boost')
        ->andReturn(Command::FAILURE);

    Scribe::swap($mockScribe);

    $command = new StartCommand;

    expect($command->handle())->toBe(Command::FAILURE);
});
