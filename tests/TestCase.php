<?php

namespace Tests;

use App\Contracts\Rcon;
use App\Services\FakeRcon;
use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Seed once with migrate:fresh. Pest applies the RefreshDatabase trait
     * (see tests/Pest.php), which shadows any method overrides here - these
     * properties are the supported way to hook the stock refresh flow.
     */
    protected $seed = true;

    protected $seeder = TestingSeeder::class;

    protected FakeRcon $rcon;

    /** The database most recently prepared by RefreshDatabase. */
    private static ?string $preparedDatabase = null;

    /** The database this test runs against. */
    protected function databaseName(): string
    {
        return (string) env('DB_DATABASE', 'testing');
    }

    /** The emulator whose schema that database holds. */
    protected function emulatorDriver(): string
    {
        return 'arcturus';
    }

    /**
     * Each emulator owns a database of its own, so the environment is set
     * explicitly rather than inherited - otherwise the driver a previous test
     * selected would follow this one into a database that does not match it.
     *
     * RefreshDatabase only remembers whether it has migrated, not what it
     * migrated, so switching databases has to reset that.
     */
    public function createApplication(): Application
    {
        foreach (['DB_DATABASE' => $this->databaseName(), 'EMULATOR_DRIVER' => $this->emulatorDriver()] as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $_SERVER[$key] = $value;
        }

        if (self::$preparedDatabase !== $this->databaseName()) {
            RefreshDatabaseState::$migrated = false;
            self::$preparedDatabase = $this->databaseName();
        }

        return $this->createLaravelApplication();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Tests must not depend on compiled frontend assets (CI never builds them).
        $this->withoutVite();

        // Never open the emulator socket in tests; disconnected by default so
        // services take their database path.
        $this->rcon = new FakeRcon;

        if ($this->fakesRcon()) {
            $this->app->instance(Rcon::class, $this->rcon);
        }
    }

    /**
     * Whether to stand in for the driver's RCON bridge. Drivers that have no
     * socket to open are left alone, so their own behaviour is what runs.
     */
    protected function fakesRcon(): bool
    {
        return true;
    }
}
