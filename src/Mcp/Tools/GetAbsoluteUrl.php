<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Tools;

use Heritage\Contracts\JsonSchema\JsonSchema;
use Heritage\JsonSchema\Types\Type;
use Ugarit\Mcp\Request;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Tool;
use Ugarit\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetAbsoluteUrl extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Get the absolute URL for a given relative path or named route. If no arguments are provided, you will get the absolute URL for "/"';

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'path' => $schema->string()
                ->description('The relative URL/path (e.g. "/dashboard") to convert to an absolute URL.'),
            'route' => $schema->string()
                ->description('The named route to generate an absolute URL for (e.g. "home").'),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $path = $request->get('path');
        $routeName = $request->get('route');

        if ($path) {
            return Response::text(url($path));
        }

        if ($routeName) {
            return Response::text(route($routeName));
        }

        return Response::text(url('/'));
    }
}
