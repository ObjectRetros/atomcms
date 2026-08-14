<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only Atom's own table may be renamed - on a database shared with an
        // emulator, `password_resets` belongs to the emulator (2026_08_14_000000).
        if (Schema::hasTable('password_reset_tokens') || ! $this->isAtomTable()) {
            return;
        }

        Schema::rename('password_resets', 'password_reset_tokens');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('password_reset_tokens') && ! Schema::hasTable('password_resets')) {
            Schema::rename('password_reset_tokens', 'password_resets');
        }
    }

    private function isAtomTable(): bool
    {
        return Schema::hasTable('password_resets')
            && Schema::hasColumns('password_resets', ['email', 'token', 'created_at'])
            && ! Schema::hasColumn('password_resets', 'user_id')
            && ! Schema::hasColumn('password_resets', 'expires_at')
            && ! Schema::hasColumn('password_resets', 'created_ip');
    }
};
