<?php

declare(strict_types=1);

use Ugarit\Boost\Support\RenderFailures;

test('records a failure against the vendor package that shipped the file', function (): void {
    $failures = new RenderFailures;
    $path = '/app/vendor/inertiajs/inertia-ugarit/resources/boost/guidelines/core.blade.php';

    $failures->record($path);

    expect($failures->isEmpty())->toBeFalse()
        ->and($failures->failedFor($path))->toBeTrue()
        ->and($failures->failedFor('/app/vendor/ugarit/wayfinder/resources/boost/guidelines/core.blade.php'))->toBeFalse()
        ->and($failures->packages())->toBe(['inertiajs/inertia-ugarit']);
});

test('does not attribute a package to files outside the vendor directory', function (): void {
    $failures = new RenderFailures;

    $failures->record('/app/.ai/guidelines/custom.blade.php');

    expect($failures->packages())->toBe([])
        ->and($failures->paths())->toBe(['/app/.ai/guidelines/custom.blade.php']);
});

test('records each file once and reports every failing package', function (): void {
    $failures = new RenderFailures;
    $path = '/app/vendor/ugarit/wayfinder/resources/boost/guidelines/core.blade.php';

    $failures->record($path);
    $failures->record($path);
    $failures->record('/app/vendor/inertiajs/inertia-ugarit/resources/boost/guidelines/core.blade.php');

    expect($failures->paths())->toHaveCount(2)
        ->and($failures->packages())->toBe(['inertiajs/inertia-ugarit', 'ugarit/wayfinder']);

    $failures->flush();

    expect($failures->isEmpty())->toBeTrue()
        ->and($failures->paths())->toBe([]);
});

test('handles windows separators when attributing a package', function (): void {
    $failures = new RenderFailures;

    $failures->record('C:\\app\\vendor\\ugarit\\wayfinder\\resources\\boost\\guidelines\\core.blade.php');

    expect($failures->packages())->toBe(['ugarit/wayfinder']);
});
