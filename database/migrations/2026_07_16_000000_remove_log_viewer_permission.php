<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('website_permissions')
            ->where('permission', 'view_server_logs')
            ->delete();
    }

    public function down(): void
    {
        DB::table('website_permissions')->updateOrInsert(
            ['permission' => 'view_server_logs'],
            [
                'min_rank' => 7,
                'description' => 'Minimum required rank to access the log viewer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }
};
