<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE website_open_positions MODIFY permission_id INT(11) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE website_open_positions MODIFY permission_id INT(11) NOT NULL');
    }
};
