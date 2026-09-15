<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Prompts\UpgradeUgaritv13;

use Ugarit\Boost\Concerns\RendersBladeGuidelines;
use Ugarit\Boost\Install\Herd;
use Ugarit\Boost\Support\PackageRegistry;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Prompt;
use Ugarit\Roster\ProjectManager;

class UpgradeUgaritV13 extends Prompt
{
    use RendersBladeGuidelines;

    protected string $name = 'upgrade-ugarit-v13';

    protected string $title = 'upgrade_ugarit_v13';

    protected string $description = 'Provides step-by-step guidance for upgrading from Ugarit 12.x to 13.0.';

    public function shouldRegister(ProjectManager $project): bool
    {
        return $project->php()->uses(PackageRegistry::UGARIT, '>=12.0.0 <13.0.0');
    }

    public function handle(): Response
    {
        $content = $this->renderBladeFile(__DIR__.'/upgrade-ugarit-v13.blade.php', [
            'usesHerd' => app(Herd::class)->isInstalled(),
        ]);

        return Response::text($content);
    }
}
