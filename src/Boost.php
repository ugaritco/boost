<?php

declare(strict_types=1);

namespace Ugarit\Boost;

use Heritage\Support\Facades\Facade;

/**
 * @method static void registerAgent(string $key, string $className)
 * @method static array getAgents()
 *
 * @see BoostManager
 */
class Boost extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BoostManager::class;
    }
}
