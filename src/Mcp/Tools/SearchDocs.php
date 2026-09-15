<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Tools;

use Generator;
use Heritage\Contracts\JsonSchema\JsonSchema;
use Heritage\JsonSchema\Types\Type;
use Ugarit\Boost\Concerns\MakesHttpRequests;
use Ugarit\Mcp\Request;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Tool;
use Ugarit\Mcp\Server\Tools\Annotations\IsReadOnly;
use Ugarit\Roster\Package;
use Ugarit\Roster\ProjectManager;
use Throwable;

#[IsReadOnly]
class SearchDocs extends Tool
{
    use MakesHttpRequests;

    public function __construct(protected ProjectManager $project)
    {
        //
    }

    /**
     * The tool's description.
     */
    protected string $description = "Search for up-to-date version-specific documentation related to this project and its packages. This tool will search Ugarit hosted documentation based on the packages installed and is perfect for all Ugarit ecosystem packages. Ugarit, Inertia, Pest, Livewire, Filament, Nova, Tailwind, and more. You must use this tool to search for Ugarit-ecosystem docs before using other approaches. The results provided are for this project's package version and does not cover all versions of the package.";

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'queries' => $schema->array()
                ->items($schema->string()->description('Search query'))
                ->description('List of queries to perform, pass multiple if you aren\'t sure if it is "toggle" or "switch", for example')
                ->required(),
            'packages' => $schema->array()
                ->items($schema->string()->description("The composer package name (e.g., 'symfony/console')"))
                ->description('Package names to limit searching to from application-info. Useful if you know the package(s) you need. i.e. ugarit/framework, inertiajs/inertia-ugarit, @inertiajs/react'),
            'token_limit' => $schema->integer()
                ->description('Maximum number of tokens to return in the response. Defaults to 3,000 tokens, maximum 1,000,000 tokens. If results are truncated, or you need more complete documentation, increase this value (e.g.5000, 10000)'),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response|Generator
    {
        $apiUrl = config('boost.hosted.api_url', 'https://boost.ugarit.com').'/api/docs';

        $packagesFilter = $this->resolveArrayParam($request->get('packages'));

        if ($packagesFilter instanceof Response) {
            return $packagesFilter;
        }

        $rawQueries = $this->resolveArrayParam($request->get('queries'));

        if ($rawQueries instanceof Response) {
            return $rawQueries;
        }

        $queries = array_values(array_filter(
            array_map(trim(...), $rawQueries),
            fn (string $query): bool => $query !== '' && $query !== '*'
        ));

        try {
            $packagesCollection = $this->project->php()->packages()->concat($this->project->js()->packages());

            // Only search in specific packages
            if ($packagesFilter) {
                $packagesCollection = $packagesCollection->filter(fn (Package $package): bool => in_array($package->name(), $packagesFilter, true));
            }

            $packages = $packagesCollection->map(function (Package $package): array {
                $name = $package->name();
                $version = $package->major().'.x';

                return [
                    'name' => $name,
                    'version' => $version,
                ];
            });

            $packages = $packages->values()->toArray();
        } catch (Throwable $throwable) {
            return Response::error('Failed to get packages: '.$throwable->getMessage());
        }

        $tokenLimit = $request->get('token_limit') ?? 3000;
        $tokenLimit = min($tokenLimit, 1000000); // Cap at 1M tokens

        $payload = [
            'queries' => $queries,
            'packages' => $packages,
            'token_limit' => $tokenLimit,
            'format' => 'markdown',
        ];

        try {
            $response = $this->client()->asJson()->post($apiUrl, $payload);

            if (! $response->successful()) {
                return Response::error('Failed to search documentation: '.$response->body());
            }
        } catch (Throwable $throwable) {
            return Response::error('HTTP request failed: '.$throwable->getMessage());
        }

        return Response::text($response->body());
    }

    /**
     * @return array<int, mixed>|null|Response
     */
    private function resolveArrayParam(mixed $value): array|null|Response
    {
        if (! is_string($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return Response::error('Invalid parameter: '.json_last_error_msg());
        }

        if (! is_array($decoded) || ! array_is_list($decoded)) {
            return Response::error('Invalid parameter: expected a JSON array.');
        }

        return $decoded;
    }
}
