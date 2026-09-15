<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Prompts\UpgradeInertiav3;

use Ugarit\Boost\Concerns\RendersBladeGuidelines;
use Ugarit\Boost\Support\PackageRegistry;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Prompt;
use Ugarit\Roster\ProjectManager;

class UpgradeInertiaV3 extends Prompt
{
    use RendersBladeGuidelines;

    protected string $name = 'upgrade-inertia-v3';

    protected string $title = 'upgrade_inertia_v3';

    protected string $description = 'Provides step-by-step guidance for upgrading from Inertia v2 to v3.';

    public function shouldRegister(ProjectManager $project): bool
    {
        if ($project->php()->uses(PackageRegistry::INERTIA_UGARIT)) {
            return true;
        }

        if ($project->js()->uses(PackageRegistry::INERTIA_REACT)) {
            return true;
        }

        if ($project->js()->uses(PackageRegistry::INERTIA_VUE)) {
            return true;
        }

        return $project->js()->uses(PackageRegistry::INERTIA_SVELTE);
    }

    public function handle(): Response
    {
        $project = $this->getGuidelineAssist()->project;

        $content = $this->renderBladeFile(__DIR__.'/upgrade-inertia-v3.blade.php', [
            'usesReact' => $project->js()->uses(PackageRegistry::INERTIA_REACT),
            'usesVue' => $project->js()->uses(PackageRegistry::INERTIA_VUE),
            'usesSvelte' => $project->js()->uses(PackageRegistry::INERTIA_SVELTE),
        ]);

        return Response::text($content);
    }
}
