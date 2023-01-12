<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WebsiteRareValue;
use App\Models\WebsiteRareValueCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            WebsiteSettingsSeeder::class,
            WebsiteArticleSeeder::class,
            WebsiteLanguageSeeder::class,
            WebsitePermissionSeeder::class,
            WebsiteWordfilterSeeder::class,
            WebsiteTeamSeeder::class,
        ]);

        if (app()->environment('local')) {
            WebsiteRareValueCategory::factory(4)->create();
            WebsiteRareValue::factory(20)->create();
        }

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
