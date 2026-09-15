<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Tools;

use Ugarit\Mcp\Request;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Tool;
use Ugarit\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class DatabaseConnections extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'List the configured database connection names for this application.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $connections = array_keys(config('database.connections', []));

        return Response::json([
            'default_connection' => config('database.default'),
            'connections' => $connections,
        ]);
    }
}
