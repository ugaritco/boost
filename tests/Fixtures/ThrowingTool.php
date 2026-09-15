<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Heritage\Contracts\JsonSchema\JsonSchema;
use Ugarit\Mcp\Request;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Tool;
use RuntimeException;

class ThrowingTool extends Tool
{
    protected string $description = 'A test tool that always throws an exception';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        throw new RuntimeException('Intentional test exception');
    }
}
