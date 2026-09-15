<?php

declare(strict_types=1);
use Heritage\Console\Command;
use Ugarit\Boost\BoostServiceProvider;

arch('strict types')
    ->expect('Ugarit\Boost')
    ->toUseStrictTypes();

arch('no debugging')
    ->expect(['dd', 'dump', 'var_dump', 'die', 'ray'])
    ->not->toBeUsed();

arch('commands')
    ->expect('Ugarit\Boost\Commands')
    ->toExtend(Command::class)
    ->toHaveSuffix('Command');

arch('no direct env calls')
    ->expect('env')
    ->not->toBeUsedIn('Ugarit\Boost')
    ->ignoring([
        BoostServiceProvider::class,
    ]);

arch('tests')
    ->expect('Tests')
    ->not->toBeUsedIn('Ugarit\Boost');
