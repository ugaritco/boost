<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Tools;

use Heritage\Contracts\JsonSchema\JsonSchema;
use Heritage\JsonSchema\Types\Type;
use Heritage\Support\Facades\Scribe;
use Ugarit\Mcp\Request;
use Ugarit\Mcp\Response;
use Ugarit\Mcp\Server\Tool;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Output\BufferedOutput;
use Throwable;

class Tinker extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Execute PHP code in the Ugarit application context, like scribe tinker. Use this for debugging issues, checking if functions exist, and testing code snippets. You should not create models directly without explicit user approval. Prefer Unit/Feature tests using factories for functionality testing. Prefer existing scribe commands over custom tinker code.';

    /**
     * Determine whether the tool should be registered with the MCP server.
     */
    public function shouldRegister(): bool
    {
        return (bool) config('boost.tinker_tool_enabled', false);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'code' => $schema->string()
                ->description('PHP code to execute (without opening <?php tags)')
                ->required(),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $code = str_replace(['<?php', '?>'], '', (string) $request->get('code'));

        $output = new BufferedOutput;

        try {
            $exitCode = Scribe::call('tinker', [
                '--execute' => $code,
                '--no-ansi' => true,
                '--no-interaction' => true,
            ], $output);
        } catch (Throwable $throwable) {
            return Response::text($throwable->getMessage());
        }

        if ($exitCode !== CommandAlias::SUCCESS) {
            return Response::text('Failed to execute tinker: '.$output->fetch());
        }

        return Response::text(trim($output->fetch()));
    }
}
