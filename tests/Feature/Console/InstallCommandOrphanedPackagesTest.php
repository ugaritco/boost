<?php

declare(strict_types=1);

use Ugarit\Boost\Install\ThirdPartyPackage;
use Ugarit\Boost\Support\Config;
use Ugarit\Prompts\Key;
use Ugarit\Prompts\Prompt;

use function Ugarit\Prompts\multiselect;

beforeEach(function (): void {
    (new Config)->flush();
});

it('passes only valid defaults to multiselect when orphaned packages exist in config', function (): void {
    Prompt::fake([Key::ENTER]);

    $configuredPackages = ['valid-pkg', 'orphaned-pkg'];

    $discoveredPackages = collect([
        'valid-pkg' => new ThirdPartyPackage('valid-pkg', true, false),
    ]);

    $validDefaults = collect($configuredPackages)
        ->filter(fn (string $name) => $discoveredPackages->has($name))
        ->values()
        ->toArray();

    $result = multiselect(
        label: 'Select packages',
        options: $discoveredPackages->mapWithKeys(fn (ThirdPartyPackage $pkg, string $name): array => [
            $name => $pkg->displayLabel(),
        ])->toArray(),
        default: $validDefaults,
    );

    expect($result)->toContain('valid-pkg')
        ->and($result)->not->toContain('orphaned-pkg');
})->skipOnWindows();
