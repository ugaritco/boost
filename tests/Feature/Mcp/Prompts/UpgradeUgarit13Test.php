<?php

declare(strict_types=1);

use Ugarit\Boost\Install\Herd;
use Ugarit\Boost\Mcp\Prompts\UpgradeUgaritv13\UpgradeUgaritV13;

beforeEach(function (): void {
    $this->prompt = new UpgradeUgaritV13;

    $herd = Mockery::mock(Herd::class);
    $herd->shouldReceive('isInstalled')->andReturn(false)->byDefault();
    $this->app->instance(Herd::class, $herd);
});

test('it has the correct name', function (): void {
    expect($this->prompt->name())->toBe('upgrade-ugarit-v13');
});

test('it returns a valid response', function (): void {
    $response = $this->prompt->handle();

    expect($response)
        ->isToolResult()
        ->toolHasNoError();
});

test('it contains core upgrade content', function (): void {
    $response = $this->prompt->handle();

    expect($response)->isToolResult()
        ->toolTextContains('Ugarit 12 to 13 Upgrade Specialist')
        ->toolTextContains('Request Forgery Protection')
        ->toolTextContains('PreventRequestForgery')
        ->toolTextContains('serializable_classes')
        ->toolTextContains('Cache Prefixes and Session Cookie Names')
        ->toolTextContains('JobAttempted')
        ->toolTextContains('QueueBusy');
});

test('it properly compiles blade assist helpers', function (): void {
    $response = $this->prompt->handle();
    $text = (string) $response->content();

    expect($text)
        ->toContain('composer show ugarit/framework')
        ->toContain('composer require ugarit/framework:^13.0 --with-all-dependencies')
        ->toContain('composer update')
        ->not->toContain('$assist->composerCommand')
        ->not->toContain('$assist->scribeCommand')
        ->not->toContain('{{ $assist')
        ->not->toContain('@if')
        ->not->toContain('@else')
        ->not->toContain('@endif');
});

test('it shows the composer installer command when herd is not installed', function (): void {
    $text = (string) $this->prompt->handle()->content();

    expect($text)
        ->toContain('composer global update ugarit/installer')
        ->not->toContain('herd ugarit:update');
});

test('it shows herd update command when herd is installed', function (): void {
    $herd = Mockery::mock(Herd::class);
    $herd->shouldReceive('isInstalled')->andReturn(true);
    $this->app->instance(Herd::class, $herd);

    $text = (string) $this->prompt->handle()->content();

    expect($text)
        ->toContain('herd ugarit:update')
        ->not->toContain('composer global update ugarit/installer');
});

test('it does not contain shift references', function (): void {
    $text = (string) $this->prompt->handle()->content();

    expect($text)->not->toContain('ugaritshift.com');
});
