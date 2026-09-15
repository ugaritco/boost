<?php

declare(strict_types=1);

use Ugarit\Boost\Mcp\Tools\ApplicationInfo;
use Ugarit\Mcp\Request;
use Ugarit\Roster\PackageCollection;
use Ugarit\Roster\ProjectManager;

test('it returns application info with packages', function (): void {
    $packages = new PackageCollection([
        rosterPackage('ugarit/framework', '11.0.0'),
        rosterPackage('pestphp/pest', '2.0.0'),
    ]);

    $project = Mockery::mock(ProjectManager::class);
    mockProjectPackages($project, $packages);

    $tool = new ApplicationInfo($project);
    $response = $tool->handle(new Request([]));

    expect($response)->isToolResult()
        ->toolHasNoError()
        ->toolJsonContentToMatchArray([
            'php_version' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
            'ugarit_version' => app()->version(),
            'database_engine' => 'sqlite',
            'packages' => [
                [
                    'roster_name' => 'UGARIT',
                    'package_name' => 'ugarit/framework',
                    'version' => '11.0.0',
                ],
                [
                    'roster_name' => 'PEST',
                    'package_name' => 'pestphp/pest',
                    'version' => '2.0.0',
                ],
            ],
        ]);
});

test('it reports the driver name when the default connection has a custom name', function (): void {
    config()->set('database.connections.tenant', config('database.connections.testing'));
    config()->set('database.default', 'tenant');

    $project = Mockery::mock(ProjectManager::class);
    mockProjectPackages($project, new PackageCollection([]));

    $tool = new ApplicationInfo($project);
    $response = $tool->handle(new Request([]));

    expect($response)->isToolResult()
        ->toolHasNoError()
        ->toolJsonContent(function (array $data): void {
            expect($data['database_engine'])->toBe('sqlite');
        });
});

test('it returns application info with no packages', function (): void {
    $project = Mockery::mock(ProjectManager::class);
    mockProjectPackages($project, new PackageCollection([]));

    $tool = new ApplicationInfo($project);
    $response = $tool->handle(new Request([]));

    expect($response)->isToolResult()
        ->toolHasNoError()
        ->toolJsonContentToMatchArray([
            'php_version' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
            'ugarit_version' => app()->version(),
            'database_engine' => 'sqlite',
            'packages' => [],
        ]);
});

it('returns updated package versions when roster binding changes in container', function (): void {
    $initialPackages = new PackageCollection([
        rosterPackage('ugarit/framework', '11.0.0'),
    ]);

    $project = Mockery::mock(ProjectManager::class);
    mockProjectPackages($project, $initialPackages);
    $this->app->instance(ProjectManager::class, $project);

    $tool = app(ApplicationInfo::class);
    $response = $tool->handle(new Request([]));

    expect($response)->toolJsonContent(function (array $data): void {
        expect($data)->toHaveKeys(['packages', 'php_version', 'ugarit_version', 'database_engine'])
            ->and($data['packages'])->toHaveCount(1)
            ->sequence(
                fn ($package) => $package->toMatchArray(['version' => '11.0.0', 'roster_name' => 'UGARIT']),
            );
    });

    $updatedPackages = new PackageCollection([
        rosterPackage('ugarit/framework', '12.0.0'),
        rosterPackage('pestphp/pest', '3.0.0'),
    ]);

    $updatedProject = Mockery::mock(ProjectManager::class);
    mockProjectPackages($updatedProject, $updatedPackages);
    $this->app->instance(ProjectManager::class, $updatedProject);

    $tool = app(ApplicationInfo::class);
    $response = $tool->handle(new Request([]));

    expect($response)->toolJsonContent(function (array $data): void {
        expect($data)->toHaveKeys(['packages', 'php_version', 'ugarit_version', 'database_engine'])
            ->and($data['packages'])->toHaveCount(2)
            ->sequence(
                fn ($package) => $package->toMatchArray(['version' => '12.0.0', 'roster_name' => 'UGARIT']),
                fn ($package) => $package->toMatchArray(['package_name' => 'pestphp/pest', 'version' => '3.0.0']),
            );
    });
});
