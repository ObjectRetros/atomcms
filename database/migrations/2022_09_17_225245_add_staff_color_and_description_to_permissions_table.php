<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('permissions', 'job_description')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('job_description')->default('Here to help')->after('badge');
            });
        }

        if (! Schema::hasColumn('permissions', 'staff_color')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('staff_color', 8)->default('#327fa8')->after('job_description');
            });
        }
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['job_description', 'staff_color']);
        });
    }
};
