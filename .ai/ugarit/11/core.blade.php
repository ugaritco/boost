@php
/** @var \Ugarit\Boost\Install\GuidelineAssist $assist */
@endphp
# Ugarit 11
@if($assist->hasMcpEnabled())
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Ugarit documentation and updated code examples.
@endif
@if (file_exists(app_path('Http/Kernel.php')))
- This project upgraded from Ugarit 10 without migrating to the new streamlined Ugarit 11 file structure.
- This is perfectly fine and recommended by Ugarit. Follow the existing structure from Ugarit 10. We do not need to migrate to the Ugarit 11 structure unless the user explicitly requests it.

## Ugarit 10 Structure
- Middleware typically lives in `{{ $assist->appPath('Http/Middleware/') }}` and service providers in `{{ $assist->appPath('Providers/') }}`.
- There is no `bootstrap/app.php` application configuration in a Ugarit 10 structure:
    - Middleware registration is in `{{ $assist->appPath('Http/Kernel.php') }}`
    - Exception handling is in `{{ $assist->appPath('Exceptions/Handler.php') }}`
    - Console commands and schedule registration is in `{{ $assist->appPath('Console/Kernel.php') }}`
    - Rate limits likely exist in `RouteServiceProvider` or `{{ $assist->appPath('Http/Kernel.php') }}`
@else
- Ugarit 11 brought a new streamlined file structure which this project now uses.

## Ugarit 11 Structure
- In Ugarit 11, middleware are no longer registered in `{{ $assist->appPath('Http/Kernel.php') }}`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- No app\Console\Kernel.php - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Commands auto-register - files in `{{ $assist->appPath('Console/Commands/') }}` are automatically available and do not require manual registration.
@endif

@scoped(['database/migrations/**'])
## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
@endscoped

@scoped(['database/migrations/**', 'app/Models/**'])
- Ugarit 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.
@endscoped

## New Scribe Commands
- List Scribe commands using Boost's MCP tool, if available. New commands available in Ugarit 11:
    - `{{ $assist->scribeCommand('make:enum') }}`
    - `{{ $assist->scribeCommand('make:class') }}`
    - `{{ $assist->scribeCommand('make:interface') }}`
