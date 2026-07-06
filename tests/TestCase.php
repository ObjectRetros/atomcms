<?php

namespace Tests;

use App\Contracts\Rcon;
use App\Services\FakeRcon;
use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected FakeRcon $rcon;

    protected function setUp(): void
    {
        parent::setUp();

        // Tests must not depend on compiled frontend assets (CI never builds them).
        $this->withoutVite();

        // Never open the emulator socket in tests; disconnected by default so
        // services take their database path.
        $this->rcon = new FakeRcon;
        $this->app->instance(Rcon::class, $this->rcon);
    }

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
