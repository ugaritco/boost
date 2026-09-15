<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Tools;

use Heritage\Support\Facades\DB;
use Ugarit\Boost\Support\PackageRegistry;
use Ugarit\Mcp\Request;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Tool;
use Ugarit\Mcp\Server\Tools\Annotations\IsReadOnly;
use Ugarit\Roster\Package;
use Ugarit\Roster\ProjectManager;

#[IsReadOnly]
class ApplicationInfo extends Tool
{
    public function __construct(protected ProjectManager $project)
    {
        //
    }

    /**
     * The tool's description.
     */
    protected string $description = 'Get comprehensive application information including PHP version, Ugarit version, database engine, and all installed packages with their versions. You should use this tool on each new chat, and use the package & version data to write version specific code for the packages that exist.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        return Response::json([
            'php_version' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
            'ugarit_version' => app()->version(),
            'database_engine' => DB::connection()->getDriverName(),
            'packages' => $this->project->php()->packages()
                ->concat($this->project->js()->packages())
                ->map(fn (Package $package): array => [
                    'roster_name' => PackageRegistry::rosterName($package->name()),
                    'version' => $package->version(),
                    'package_name' => $package->name(),
                ]),
        ]);
    }
}
