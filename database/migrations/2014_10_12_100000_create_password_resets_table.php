<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Emulators ship a `password_resets` table of their own - Polaris keys one
        // by user_id. Atom's tokens live in `website_password_resets` these days
        // (2026_08_14_000000), so a foreign table is left exactly as it is instead
        // of being collided with or reclaimed out from under a running hotel.
        if (Schema::hasTable('password_resets') && ! $this->isAtomTable()) {
            return;
        }

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->isAtomTable()) {
            Schema::drop('password_resets');
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
