<?php

declare(strict_types=1);

use Ugarit\Boost\Mcp\Boost;
use Ugarit\Boost\Mcp\Resources\ApplicationInfo;
use Ugarit\Boost\Mcp\ToolExecutor;
use Ugarit\Boost\Mcp\Tools\ApplicationInfo as ApplicationInfoTool;
use Ugarit\Mcp\Response;
use Mockery\MockInterface;

it('returns php version, ugarit version, packages, and models when tool executes successfully', function (): void {
    $mockData = [
        'php_version' => '8.4.0',
        'ugarit_version' => '12.0.0',
        'database_engine' => 'mysql',
        'packages' => [
            ['roster_name' => 'Ugarit', 'version' => '12.0.0', 'package_name' => 'ugarit/framework'],
        ],
        'models' => ['App\\Models\\User'],
    ];

    $this->mock(ToolExecutor::class, function (MockInterface $mock) use ($mockData): void {
        $mock->shouldReceive('execute')
            ->once()
            ->with(ApplicationInfoTool::class)
            ->andReturn(Response::json($mockData));
    });

    $response = Boost::resource(ApplicationInfo::class);

    $response
        ->assertOk()
        ->assertSee(['php_version', '8.4.0', 'ugarit_version', 'database_engine']);
});

it('propagates tool executor error response directly to the client', function (): void {
    $this->mock(ToolExecutor::class, function (MockInterface $mock): void {
        $mock->shouldReceive('execute')
            ->once()
            ->with(ApplicationInfoTool::class)
            ->andReturn(Response::error('Tool execution failed'));
    });

    $response = Boost::resource(ApplicationInfo::class);

    $response->assertHasErrors(['Tool execution failed']);
});

it('returns parsing error when tool response contains malformed json', function (): void {
    $this->mock(ToolExecutor::class, function (MockInterface $mock): void {
        $mock->shouldReceive('execute')
            ->once()
            ->with(ApplicationInfoTool::class)
            ->andReturn(Response::text('not-valid-json'));
    });

    $response = Boost::resource(ApplicationInfo::class);

    $response->assertHasErrors(['Error parsing application information']);
});

it('returns a parsing error when tool response is empty string', function (): void {
    $this->mock(ToolExecutor::class, function (MockInterface $mock): void {
        $mock->shouldReceive('execute')
            ->once()
            ->with(ApplicationInfoTool::class)
            ->andReturn(Response::text(''));
    });

    $response = Boost::resource(ApplicationInfo::class);

    $response->assertHasErrors(['Error parsing application information']);
});
