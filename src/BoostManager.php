<?php

declare(strict_types=1);

namespace Ugarit\Boost;

use InvalidArgumentException;
use Ugarit\Boost\Install\Agents\Agent;
use Ugarit\Boost\Install\Agents\Amp;
use Ugarit\Boost\Install\Agents\Antigravity;
use Ugarit\Boost\Install\Agents\ClaudeCode;
use Ugarit\Boost\Install\Agents\Codex;
use Ugarit\Boost\Install\Agents\Copilot;
use Ugarit\Boost\Install\Agents\Cursor;
use Ugarit\Boost\Install\Agents\Factory;
use Ugarit\Boost\Install\Agents\GrokBuild;
use Ugarit\Boost\Install\Agents\Junie;
use Ugarit\Boost\Install\Agents\Kiro;
use Ugarit\Boost\Install\Agents\OpenCode;
use Ugarit\Boost\Install\Agents\Pi;
use Ugarit\Boost\Install\Agents\Zed;

class BoostManager
{
    /** @var array<string, class-string<Agent>> */
    private array $agents = [
        'amp' => Amp::class,
        'antigravity' => Antigravity::class,
        'claude_code' => ClaudeCode::class,
        'codex' => Codex::class,
        'copilot' => Copilot::class,
        'cursor' => Cursor::class,
        'factory' => Factory::class,
        'grok_build' => GrokBuild::class,
        'junie' => Junie::class,
        'kiro' => Kiro::class,
        'opencode' => OpenCode::class,
        'pi' => Pi::class,
        'zed' => Zed::class,
    ];

    /**
     * @param  class-string<Agent>  $className
     */
    public function registerAgent(string $key, string $className): void
    {
        if (array_key_exists($key, $this->agents)) {
            throw new InvalidArgumentException("Agent '{$key}' is already registered");
        }

        $this->agents[$key] = $className;
    }

    /**
     * @return array<string, class-string<Agent>>
     */
    public function getAgents(): array
    {
        $agents = $this->agents;
        ksort($agents);

        return $agents;
    }
}
