<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'two_factor_confirmed_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            });
        }

        if (! Schema::hasColumn('users', 'two_factor_confirmed')) {
            return;
        }

        DB::table('users')
            ->where('two_factor_confirmed', true)
            ->whereNotNull('two_factor_secret')
            ->whereNull('two_factor_confirmed_at')
            ->update(['two_factor_confirmed_at' => now()]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('two_factor_confirmed');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'two_factor_confirmed')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('two_factor_confirmed')->default(false)->after('two_factor_recovery_codes');
            });
        }

        DB::table('users')
            ->whereNotNull('two_factor_confirmed_at')
            ->update(['two_factor_confirmed' => true]);
    }
};
