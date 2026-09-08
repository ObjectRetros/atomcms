<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'website_remember_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('website_remember_token', 100)->nullable();
            });
        }

        // Some hotels added Laravel's default column themselves. Preserve their
        // existing cookies while leaving the emulator-owned column untouched.
        if (Schema::hasColumn('users', 'remember_token')) {
            DB::table('users')
                ->whereNull('website_remember_token')
                ->whereNotNull('remember_token')
                ->update(['website_remember_token' => DB::raw('remember_token')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('website_remember_token');
        });
    }
};
