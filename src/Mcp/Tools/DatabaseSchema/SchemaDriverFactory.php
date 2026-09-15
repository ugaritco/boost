<?php

declare(strict_types=1);

namespace Ugarit\Boost\Mcp\Tools\DatabaseSchema;

use Heritage\Support\Facades\DB;

class SchemaDriverFactory
{
    public static function make(?string $connection = null): DatabaseSchemaDriver
    {
        $connectionName = $connection ?? config('database.default');
        $driverName = config("database.connections.{$connectionName}.driver");

        if (! is_string($driverName) || $driverName === '') {
            $driverName = DB::connection($connectionName)->getDriverName();
        }

        return match ($driverName) {
            'mysql', 'mariadb' => new MySQLSchemaDriver($connection),
            'pgsql' => new PostgreSQLSchemaDriver($connection),
            'sqlite' => new SQLiteSchemaDriver($connection),
            default => new NullSchemaDriver($connection),
        };
    }
}
