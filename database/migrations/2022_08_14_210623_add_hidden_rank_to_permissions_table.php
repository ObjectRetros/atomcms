<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('permissions', 'hidden_rank')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->boolean('hidden_rank')->after('rank_name')->default(false);
            });
        }
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('hidden_rank');
        });
    }
};
