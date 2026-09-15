<?php

declare(strict_types=1);

namespace Tests\Unit\Install\Agents;

use Ugarit\Boost\Install\Agents\Copilot;
use Ugarit\Boost\Install\Detection\DetectionStrategyFactory;
use Mockery;

beforeEach(function (): void {
    $this->strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
});

test('httpMcpServerConfig returns default http config', function (): void {
    $agent = new Copilot($this->strategyFactory);

    expect($agent->httpMcpServerConfig('https://nightwatch.ugarit.com/mcp'))->toBe([
        'type' => 'http',
        'url' => 'https://nightwatch.ugarit.com/mcp',
    ]);
});
