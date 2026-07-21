<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'two_factor_confirmed')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('two_factor_confirmed')
                    ->after('two_factor_recovery_codes')
                    ->default(false);
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_confirmed');
        });
    }
};
