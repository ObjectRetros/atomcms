<?php

namespace Tests;

use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function refreshTestDatabase()
    {
        if (! RefreshDatabase::$migrated) {
            // Create database if it doesn't exist
            $this->createTestDatabase();

            // Run migrations (including CoreSqlFile migration)
            $this->artisan('migrate:fresh');

            // Force TestingSeeder to run
            $this->artisan('db:seed', ['--class' => TestingSeeder::class]);

            RefreshDatabase::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    protected function createTestDatabase(): void
    {
        $database = config('database.connections.mariadb.database');
        $connection = config('database.connections.mariadb');

        // Connect to MariaDB without specifying database
        $tempConnection = [
            'driver' => 'mysql',
            'host' => $connection['host'],
            'port' => $connection['port'],
            'username' => $connection['username'],
            'password' => $connection['password'],
        ];

        config(['database.connections.temp' => $tempConnection]);

        DB::connection('temp')->statement("CREATE DATABASE IF NOT EXISTS `{$database}`");
        DB::purge('temp');
    }
}
