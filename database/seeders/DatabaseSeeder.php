<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // General seeders
            WebsiteSettingsSeeder::class,
            WebsiteLanguageSeeder::class,
            WebsitePermissionSeeder::class,
            WebsiteWordfilterSeeder::class,
            WebsiteMaintenanceTasksSeeder::class,

            WebsiteArticleSeeder::class,
            WebsiteTeamSeeder::class,

            // Shop
            WebsiteShopCategoriesSeeder::class,
            WebsiteShopArticleSeeder::class,

            // Help center
            WebsiteRuleCategorySeeder::class,
            WebsiteRuleSeeder::class,
            WebsiteHelperCenterCategorySeeder::class,
            WebsiteHelperCenterCategorySeeder::class,

            // Values
            WebsiteRareValuesCategorySeeder::class,

            // Housekeeping permissions
            HousekeepingPermissionSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
