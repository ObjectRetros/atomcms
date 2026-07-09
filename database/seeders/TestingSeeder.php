<?php

namespace Database\Seeders;

use App\Models\Miscellaneous\WebsiteInstallation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TestingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        WebsiteInstallation::query()->firstOrCreate(['installation_key' => 'key'], ['completed' => true]);

        $this->call([
            WebsiteSettingsSeeder::class,
            WebsiteLanguageSeeder::class,
            WebsitePermissionSeeder::class,
        ]);

        $this->createPlusEmulatorSchema();
    }

    /**
     * The core SQL file ships the Arcturus schema; add the Plus EMU tables and
     * columns the Plus drivers touch, so both emulator drivers can be
     * conformance-tested against the one testing database.
     */
    private function createPlusEmulatorSchema(): void
    {
        if (! Schema::hasColumn('users', 'activity_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('activity_points')->default(0);
                $table->integer('vip_points')->default(0);
                $table->integer('gotw_points')->default(0);
            });
        }

        if (! Schema::hasTable('user_stats')) {
            Schema::create('user_stats', function (Blueprint $table) {
                $table->integer('id')->primary()->comment('Plus keys user_stats by the user id');
                $table->integer('OnlineTime')->default(0);
                $table->integer('Respect')->default(0);
                $table->integer('AchievementScore')->default(0);
            });
        }

        if (! Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->index();
                $table->string('badge_id', 100);
                $table->integer('badge_slot')->default(0);
            });
        }
    }
}
