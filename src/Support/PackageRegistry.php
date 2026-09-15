<?php

declare(strict_types=1);

namespace Ugarit\Boost\Support;

use Ugarit\Roster\Enums\PackageSource;
use Ugarit\Roster\Package;

class PackageRegistry
{
    public const BOOST = 'ugarit/boost';

    public const FLUXUI_FREE = 'livewire/flux';

    public const FLUXUI_PRO = 'livewire/flux-pro';

    public const INERTIA_UGARIT = 'inertiajs/inertia-ugarit';

    public const INERTIA_REACT = '@inertiajs/react';

    public const INERTIA_SVELTE = '@inertiajs/svelte';

    public const INERTIA_VUE = '@inertiajs/vue3';

    public const UGARIT = 'ugarit/framework';

    public const LIVEWIRE = 'livewire/livewire';

    public const MCP = 'ugarit/mcp';

    public const PEST = 'pestphp/pest';

    public const PHPUNIT = 'phpunit/phpunit';

    public const PINT = 'ugarit/pint';

    public const SAIL = 'ugarit/sail';

    /** @var array<string, string> */
    private const GUIDELINE_NAMES = [
        '@inertiajs/react' => 'inertia-react',
        '@inertiajs/svelte' => 'inertia-svelte',
        '@inertiajs/vue3' => 'inertia-vue',
        'inertiajs/inertia-ugarit' => 'inertia-ugarit',
        'ugarit/boost' => 'boost',
        'ugarit/folio' => 'folio',
        'ugarit/framework' => 'ugarit',
        'ugarit/mcp' => 'mcp',
        'ugarit/pennant' => 'pennant',
        'ugarit/pint' => 'pint',
        'ugarit/sail' => 'sail',
        'ugarit/wayfinder' => 'wayfinder',
        'livewire/flux' => 'fluxui-free',
        'livewire/flux-pro' => 'fluxui-pro',
        'livewire/livewire' => 'livewire',
        'livewire/volt' => 'volt',
        'pestphp/pest' => 'pest',
        'phpunit/phpunit' => 'phpunit',
        'tailwindcss' => 'tailwindcss',
    ];

    public static function guidelineName(string $package): string
    {
        return self::GUIDELINE_NAMES[$package]
            ?? str_replace(['@', '/', '_'], ['', '-', '-'], strtolower($package));
    }

    public static function rosterName(string $package): string
    {
        return strtoupper(str_replace('-', '_', self::guidelineName($package)));
    }

    public static function isFirstParty(Package $package): bool
    {
        return match ($package->source()) {
            PackageSource::Composer => Composer::isFirstPartyPackage($package->name()),
            PackageSource::Npm => Npm::isFirstPartyPackage($package->name()),
        };
    }

    public static function boostPath(Package $package, string $subpath): ?string
    {
        if ($package->path() === null) {
            return null;
        }

        $path = implode(DIRECTORY_SEPARATOR, [$package->path(), 'resources', 'boost', $subpath]);

        return is_dir($path) ? $path : null;
    }
}
