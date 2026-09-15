<?php

declare(strict_types=1);

namespace Ugarit\Boost\Install\Detection;

use Heritage\Support\Facades\Process;
use Ugarit\Boost\Install\Contracts\DetectionStrategy;
use Ugarit\Boost\Install\Enums\Platform;
use Symfony\Component\Process\Exception\ProcessSignaledException;

class CommandDetectionStrategy implements DetectionStrategy
{
    public function detect(array $config, ?Platform $platform = null): bool
    {
        if (! isset($config['command'])) {
            return false;
        }

        try {
            return Process::run($config['command'])->successful();
        } catch (ProcessSignaledException) {
            return false;
        }
    }
}
