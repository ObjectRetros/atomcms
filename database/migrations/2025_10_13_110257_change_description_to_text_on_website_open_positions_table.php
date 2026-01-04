<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE website_open_positions MODIFY description TEXT NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE website_open_positions MODIFY description VARCHAR(255) NOT NULL');
    }
};
