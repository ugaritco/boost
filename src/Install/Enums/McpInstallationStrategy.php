<?php

declare(strict_types=1);

namespace Ugarit\Boost\Install\Enums;

enum McpInstallationStrategy: string
{
    case SHELL = 'shell';
    case FILE = 'file';
    case NONE = 'none';
}
