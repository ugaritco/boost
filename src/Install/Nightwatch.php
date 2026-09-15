<?php

declare(strict_types=1);

namespace Ugarit\Boost\Install;

use Ugarit\Boost\Support\Composer;

class Nightwatch
{
    const MCP_URL = 'https://nightwatch.ugarit.com/mcp';

    public function isInstalled(): bool
    {
        return array_key_exists('ugarit/nightwatch', Composer::packages());
    }

    public function mcpUrl(): string
    {
        return self::MCP_URL;
    }
}
