<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('website_settings')->updateOrInsert(
            ['key' => 'catalog_icons_path'],
            [
                'value' => '/gamedata/c_images/catalogue',
                'comment' => 'Path to catalog icons',
            ],
        );
    }

    public function down(): void
    {
        DB::table('website_settings')->whereIn('key', [
            'catalog_icons_path',
        ])->delete();
    }
};
