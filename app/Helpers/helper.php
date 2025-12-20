<?php

use App\Services\HousekeepingPermissionsService;
use App\Services\PermissionsService;
use App\Services\SettingsService;
use Illuminate\Support\Str;

if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
}

if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->getOrDefault($key, $default);
    }
}

if (! function_exists('hasPermission')) {
    function hasPermission(string $permission): string
    {
        return app(PermissionsService::class)->getOrDefault($permission);
    }
}

if (! function_exists('hasHousekeepingPermission')) {
    function hasHousekeepingPermission(string $permission): string
    {
        return app(HousekeepingPermissionsService::class)->getOrDefault($permission);
    }
}

if (! function_exists('strLimit')) {
    function strLimit(string $string, int $limit, string $replacement = '...'): string
    {
        return Str::limit($string, $limit, $replacement);
    }
}

if (! function_exists('findMigration')) {
    function findMigration(string $tableName): string
    {
        // Iterate through all migration files in the migrations directory
        foreach (glob(database_path('migrations/*.php')) as $filename) {
            // Check if the migration file has the Schema::create() line with the given table name
            if (strpos(file_get_contents($filename), "Schema::create('$tableName'")) {
                return basename($filename);
            }
        }

        // If no matching migration file is found, return an empty string
        return '';
    }
}

if (! function_exists('columnExists')) {
    function columnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }
}

if (! function_exists('dropForeignKeyIfExists')) {
    function dropForeignKeyIfExists(string $table, string $column): void
    {
        $connection = Schema::getConnection();
        $tableWithPrefix = $connection->getTablePrefix() . $table;

        $foreignKeys = collect($connection->select('SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?', [$tableWithPrefix, $column]))
            ->pluck('CONSTRAINT_NAME');

        foreach ($foreignKeys as $foreignKey) {
            if (! empty($foreignKey)) {
                $connection->statement("ALTER TABLE {$table} DROP FOREIGN KEY {$foreignKey}");
            }
        }
    }
}
