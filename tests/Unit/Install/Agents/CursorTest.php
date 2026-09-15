<?php

declare(strict_types=1);

namespace Tests\Unit\Install\Agents;

use Ugarit\Boost\Install\Agents\Cursor;
use Ugarit\Boost\Install\Detection\DetectionStrategyFactory;
use Mockery;

beforeEach(function (): void {
    $this->strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
});

test('httpMcpServerConfig returns npx mcp-remote config', function (): void {
    $agent = new Cursor($this->strategyFactory);

    expect($agent->httpMcpServerConfig('https://nightwatch.ugarit.com/mcp'))->toBe([
        'command' => 'npx',
        'args' => ['-y', 'mcp-remote', 'https://nightwatch.ugarit.com/mcp'],
    ]);
});
