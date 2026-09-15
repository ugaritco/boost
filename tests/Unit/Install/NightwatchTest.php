<?php

declare(strict_types=1);

use Ugarit\Boost\Install\Nightwatch;

test('mcpUrl returns the nightwatch mcp url', function (): void {
    $nightwatch = new Nightwatch;

    expect($nightwatch->mcpUrl())->toBe('https://nightwatch.ugarit.com/mcp');
});

test('MCP_URL constant matches mcpUrl return value', function (): void {
    expect(Nightwatch::MCP_URL)->toBe('https://nightwatch.ugarit.com/mcp');
});
