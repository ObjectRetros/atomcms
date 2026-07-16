<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('permissions', 'staff_background')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('staff_background')->default('staff-bg.png')->after('staff_color');
            });
        }
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('staff_background');
        });
    }
};
