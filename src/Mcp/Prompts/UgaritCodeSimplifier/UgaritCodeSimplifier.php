<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Prompts\UgaritCodeSimplifier;

use Ugarit\Boost\Concerns\RendersBladeGuidelines;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Prompt;

class UgaritCodeSimplifier extends Prompt
{
    use RendersBladeGuidelines;

    protected string $name = 'ugarit-code-simplifier';

    protected string $title = 'ugarit_code_simplifier';

    protected string $description = 'Simplifies and refines PHP/Ugarit code for clarity, consistency, and maintainability while preserving all functionality. Focuses on recently modified code unless instructed otherwise.';

    public function handle(): Response
    {
        $content = $this->renderBladeFile(__DIR__.'/ugarit-code-simplifier.blade.php');

        return Response::text($content);
    }
}
