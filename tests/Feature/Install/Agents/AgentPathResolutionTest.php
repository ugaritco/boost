<?php

declare(strict_types=1);

use Ugarit\Boost\Install\Agents\Cursor;
use Ugarit\Boost\Install\Agents\Junie;
use Ugarit\Boost\Install\Agents\Pi;
use Ugarit\Boost\Install\Detection\DetectionStrategyFactory;

test('Junie returns absolute PHP_BINARY path', function (): void {
    config(['boost.executable_paths.php' => null]);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $junie = new Junie($strategyFactory);

    expect($junie->getPhpPath())->toBe(PHP_BINARY);
});

test('Junie returns absolute scribe path', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $junie = new Junie($strategyFactory);

    $scribePath = $junie->getScribePath();

    // Should be an absolute path ending with 'scribe'
    expect($scribePath)->toEndWith('scribe')
        ->not->toBe('scribe');
});

test('Cursor returns relative php string', function (): void {
    config(['boost.executable_paths.php' => null]);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $cursor = new Cursor($strategyFactory);

    expect($cursor->getPhpPath())->toBe('php');
});

test('Cursor uses configured default_php_bin when not forcing absolute path', function (): void {
    config(['boost.executable_paths.php' => '/custom/path/to/php']);

    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $cursor = new Cursor($strategyFactory);

    expect($cursor->getPhpPath())->toBe('/custom/path/to/php');
});

test('Cursor uses config even when forceAbsolutePath is true', function (): void {
    config(['boost.executable_paths.php' => '/custom/path/to/php']);

    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $cursor = new Cursor($strategyFactory);

    expect($cursor->getPhpPath(true))->toBe('/custom/path/to/php');
});

test('Cursor uses PHP_BINARY when forceAbsolutePath is true and config is empty', function (): void {
    config(['boost.executable_paths.php' => null]);

    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $cursor = new Cursor($strategyFactory);

    expect($cursor->getPhpPath(true))->toBe(PHP_BINARY);
});

test('Cursor returns relative scribe path', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $cursor = new Cursor($strategyFactory);

    expect($cursor->getScribePath())->toBe('scribe');
});

test('Agents return absolute paths when forceAbsolutePath is true and config is empty', function (): void {
    config(['boost.executable_paths.php' => null]);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $cursor = new Cursor($strategyFactory);

    expect($cursor->getPhpPath(true))->toBe(PHP_BINARY)
        ->and($cursor->getScribePath(true))->toEndWith('scribe')
        ->not->toBe('scribe');
});

test('Agents maintain relative paths when forceAbsolutePath is false and config is empty', function (): void {
    config(['boost.executable_paths.php' => null]);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $cursor = new Cursor($strategyFactory);

    expect($cursor->getPhpPath())->toBe('php')
        ->and($cursor->getScribePath())->toBe('scribe');
});

test('Junie paths remain absolute regardless of forceAbsolutePath parameter', function (): void {
    config(['boost.executable_paths.php' => null]);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $junie = new Junie($strategyFactory);

    // Junie always uses absolute paths, so forceAbsolutePath shouldn't change behavior
    expect($junie->getPhpPath(true))->toBe(PHP_BINARY)
        ->and($junie->getPhpPath())->toBe(PHP_BINARY);

    $scribePath = $junie->getScribePath(true);
    expect($scribePath)->toEndWith('scribe')
        ->not->toBe('scribe')
        ->and($junie->getScribePath())->toBe($scribePath);
});

test('Junie uses config when configured', function (): void {
    config(['boost.executable_paths.php' => '/custom/php']);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $junie = new Junie($strategyFactory);

    // Config takes precedence over useAbsolutePathForMcp
    expect($junie->getPhpPath(true))->toBe('/custom/php');
    expect($junie->getPhpPath(false))->toBe('/custom/php');
});

test('Pi uses AGENTS.md and .pi/skills defaults', function (): void {
    config(['boost.executable_paths.php' => null]);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $pi = new Pi($strategyFactory);

    expect($pi->getPhpPath())->toBe('php')
        ->and($pi->getScribePath())->toBe('scribe')
        ->and($pi->guidelinesPath())->toBe('AGENTS.md')
        ->and($pi->skillsPath())->toBe('.pi/skills');
});
