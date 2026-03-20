<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('home_items')
            ->where('image', 'like', '%imgur.com/%')
            ->update(['image' => DB::raw("REPLACE(REPLACE(image, 'https://i.imgur.com/', '/assets/images/home/items/'), 'https://imgur.com/', '/assets/images/home/items/')")]);

        DB::table('home_categories')
            ->where('icon', 'like', '%imgur.com/%')
            ->update(['icon' => DB::raw("REPLACE(REPLACE(icon, 'https://i.imgur.com/', '/assets/images/home/items/'), 'https://imgur.com/', '/assets/images/home/items/')")]);
    }

    public function down(): void {}
};
