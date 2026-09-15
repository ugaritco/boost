# Upgrade Guide

## Upgrading To 2.5 From 2.4

### Guideline And Skill Authoring API

PR Link: https://github.com/ugarit/boost/pull/891

Likelihood Of Impact: Low

`boost:install` renders every Blade guideline and skill, whether you wrote it in your application's `.ai` directory or a package ships it in `resources/boost`. So you can hit this even if you never wrote one. Use the table below for files you maintain, and update any package that has not migrated yet.

Boost now runs on Ugarit Roster 1.x, which removed the `Ugarit\Roster\Enums\Packages` enum. Package names are now string constants on `Ugarit\Boost\Support\PackageRegistry`, and `$assist->roster` has become `$assist->project`, a `Ugarit\Roster\ProjectManager` that splits packages into `php()` and `js()`:

| Before                                                    | After                                                       |
|-----------------------------------------------------------|-------------------------------------------------------------|
| `\Ugarit\Roster\Enums\Packages::INERTIA_REACT`           | `\Ugarit\Boost\Support\PackageRegistry::INERTIA_REACT`     |
| `$assist->roster`                                         | `$assist->project`                                          |
| `$assist->roster->uses(Packages::X)`                      | `$assist->project->php()->uses(PackageRegistry::X)`         |
| `$assist->roster->usesVersion(Packages::X, '1.0.0', '>=')` | `$assist->project->php()->uses(PackageRegistry::X, '>=1.0.0')` |
| `$assist->roster->nodePackageManager()`                   | `$assist->project->js()->packageManager()`                  |
| `NodePackageManager::NPM`                                 | `JsPackageManager::Npm`                                     |

Before:

```blade
@if($assist->hasPackage(\Ugarit\Roster\Enums\Packages::INERTIA_REACT))
@if($assist->roster->uses(\Ugarit\Roster\Enums\Packages::INERTIA_UGARIT))
```

After:

```blade
@if($assist->hasPackage(\Ugarit\Boost\Support\PackageRegistry::INERTIA_REACT))
@if($assist->project->php()->uses(\Ugarit\Boost\Support\PackageRegistry::INERTIA_UGARIT))
```

`hasPackage()` still checks both PHP and JS packages, so reach for it when you do not care which side a package came from. `js()->uses()` also accepts an array to test several packages at once.

Guidelines and skills that still use the old API will fail to render. Boost skips those files, falls back to its own bundled copy of the guideline when it has one, and lists the packages to update once `boost:install` finishes, so a stale package no longer aborts the install. If your package needs to support both, constrain it in your `composer.json`:

```json
"conflict": {
    "ugarit/boost": "<2.5.0"
}
```

## Upgrading To 2.x From 1.x

> Note: If you are not using custom agents or overriding Boost in any way, you should experience minimal issues while upgrading. Simply run `php scribe boost:install` after upgrading to Boost 2.x and the migration will be handled automatically.

> Note: If you are using external packages that add custom agents, ensure you update to versions that have support for Boost 2.x.

### Minimum PHP Version

PHP 8.2 is now the minimum required version.

### Minimum Ugarit Version

Ugarit 11.x is now the minimum required version.

### Custom Agent Changes

PR Link: https://github.com/ugarit/boost/pull/439

Likelihood Of Impact: Low

If you have added your own custom agents, you will need to make the following changes:

#### Terminology and Namespace Changes

`CodeEnvironment` has been replaced with `Agent` throughout:

| Before                                                    | After                                           |
|-----------------------------------------------------------|-------------------------------------------------|
| `CodeEnvironment`                                         | `Agent`                                         |
| `CodeEnvironmentsDetector`                                | `AgentsDetector`                                |
| `src/Install/CodeEnvironment/`                            | `src/Install/Agents/`                           |
| `Ugarit\Boost\Install\CodeEnvironment`                   | `Ugarit\Boost\Install\Agents`                  |
| `registerCodeEnvironment(string $key, string $className)` | `registerAgent(string $key, string $className)` |
| `getCodeEnvironments()`                                   | `getAgents()`                                   |

#### Contract Renames

Several contracts have been renamed for clarity:

| Before                                  | After                                        |
|-----------------------------------------|----------------------------------------------|
| `Ugarit\Boost\Contracts\Agent`         | `Ugarit\Boost\Contracts\SupportsGuidelines` |
| `Ugarit\Boost\Contracts\McpClient`     | `Ugarit\Boost\Contracts\SupportsMcp`        |
| `Ugarit\Boost\Contracts\SupportSkills` | `Ugarit\Boost\Contracts\SupportsSkills`     |

#### Custom Agent Migration

If you have registered custom agents, update them to use the new namespace and contracts:

Before:

```php
<?php

namespace App\Boost;

use Ugarit\Boost\Contracts\Agent;
use Ugarit\Boost\Install\CodeEnvironment\CodeEnvironment;

class MyCustomAgent extends CodeEnvironment implements Agent
{
    // ...
}
```

After:

```php
<?php

namespace App\Boost;

use Ugarit\Boost\Contracts\SupportsGuidelines;
use Ugarit\Boost\Install\Agents\Agent;

class MyCustomAgent extends Agent implements SupportsGuidelines
{
    // ...
}
```

If your agent also supports MCP or skills, add the additional contracts:

```php
use Ugarit\Boost\Contracts\SupportsMcp;
use Ugarit\Boost\Contracts\SupportsSkills;

class MyCustomAgent extends Agent implements SupportsGuidelines, SupportsMcp, SupportsSkills
{
    // ...
}
```

### Configuration File Changes

PR Link: https://github.com/ugarit/boost/pull/439

Likelihood Of Impact: Low (Only applies if you have overridden configuration options in `config/boost.php`)

Published configuration paths have been updated from `code_environment` to `agents` in `config/boost.php`:

```diff
- config('boost.code_environment.junie.guidelines_path')
+ config('boost.agents.junie.guidelines_path')
```

This was previously undocumented, so the impact is very low unless you've explicitly overridden these configuration values.

### Installation Command Signature

PR Link: https://github.com/ugarit/boost/pull/439

Likelihood Of Impact: Low

The `boost:install` command flags have changed from negative opt-out to positive opt-in for clearer intent:

```diff
- php scribe boost:install {--ignore-guidelines} {--ignore-mcp}
+ php scribe boost:install {--guidelines} {--skills} {--mcp}
```
