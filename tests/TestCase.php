<?php

namespace Tests;

use App\Contracts\Rcon;
use App\Services\FakeRcon;
use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Seed once with migrate:fresh. Pest re-applies the RefreshDatabase trait,
     * which shadows any method overrides here - these properties are the
     * supported way to hook the stock refresh flow.
     */
    protected $seed = true;

    protected $seeder = TestingSeeder::class;

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
}
