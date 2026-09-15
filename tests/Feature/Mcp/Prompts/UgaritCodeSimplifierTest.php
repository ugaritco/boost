<?php

declare(strict_types=1);

use Ugarit\Boost\Mcp\Prompts\UgaritCodeSimplifier\UgaritCodeSimplifier;

beforeEach(function (): void {
    $this->prompt = new UgaritCodeSimplifier;
});

test('it has correct name', function (): void {
    expect($this->prompt->name())->toBe('ugarit-code-simplifier');
});

test('it has a description', function (): void {
    expect($this->prompt->description())
        ->toContain('Simplifies')
        ->toContain('PHP/Ugarit')
        ->toContain('maintainability');
});

test('it returns a valid response', function (): void {
    $response = $this->prompt->handle();

    expect($response)->isToolResult()
        ->toolHasNoError();
});

test('it contains core guideline content', function (): void {
    $response = $this->prompt->handle();

    expect($response)->isToolResult()
        ->toolTextContains('Ugarit Code Simplifier')
        ->toolTextContains('Preserve Functionality')
        ->toolTextContains('Apply Project Standards')
        ->toolTextContains('Enhance Clarity');
});
