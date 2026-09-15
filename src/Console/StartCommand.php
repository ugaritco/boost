<?php

declare(strict_types=1);

namespace Ugarit\Boost\Console;

use Heritage\Console\Command;
use Heritage\Support\Facades\Scribe;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('boost:mcp', 'Starts Ugarit Boost (usually from mcp.json)')]
class StartCommand extends Command
{
    public function handle(): int
    {
        return Scribe::call('mcp:start ugarit-boost');
    }
}
