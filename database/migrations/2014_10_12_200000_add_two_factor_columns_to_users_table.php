<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Fortify;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $addSecret = ! Schema::hasColumn('users', 'two_factor_secret');
        $addRecoveryCodes = ! Schema::hasColumn('users', 'two_factor_recovery_codes');
        $addConfirmedAt = Fortify::confirmsTwoFactorAuthentication()
            && ! Schema::hasColumn('users', 'two_factor_confirmed_at');

        Schema::table('users', function (Blueprint $table) use ($addConfirmedAt, $addRecoveryCodes, $addSecret) {
            if ($addSecret) {
                $table->text('two_factor_secret')
                    ->after('password')
                    ->nullable();
            }

            if ($addRecoveryCodes) {
                $table->text('two_factor_recovery_codes')
                    ->after('two_factor_secret')
                    ->nullable();
            }

            if ($addConfirmedAt) {
                $table->timestamp('two_factor_confirmed_at')
                    ->after('two_factor_recovery_codes')
                    ->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_merge([
                'two_factor_secret',
                'two_factor_recovery_codes',
            ], Fortify::confirmsTwoFactorAuthentication() ? [
                'two_factor_confirmed_at',
            ] : []));
        });
    }
};
