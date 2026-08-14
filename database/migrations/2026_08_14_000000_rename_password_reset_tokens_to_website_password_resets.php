<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables Atom kept its reset tokens in before the name was namespaced.
     */
    private const LEGACY_TABLES = ['password_reset_tokens', 'password_resets'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $legacy = $this->legacyAtomTable();

        if ($legacy !== null && ! Schema::hasTable('website_password_resets')) {
            Schema::rename($legacy, 'website_password_resets');

            return;
        }

        $this->createTable();

        if ($legacy === null) {
            return;
        }

        DB::table('website_password_resets')->insertUsing(
            ['email', 'token', 'created_at'],
            DB::table($legacy)->select('email', 'token', 'created_at'),
        );

        Schema::drop($legacy);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('website_password_resets') && ! Schema::hasTable('password_reset_tokens')) {
            Schema::rename('website_password_resets', 'password_reset_tokens');
        }
    }

    private function createTable(): void
    {
        if (Schema::hasTable('website_password_resets')) {
            return;
        }

        Schema::create('website_password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    private function legacyAtomTable(): ?string
    {
        foreach (self::LEGACY_TABLES as $table) {
            if ($this->isAtomTable($table)) {
                return $table;
            }
        }

        return null;
    }

    /**
     * The emulator ships a `password_resets` table of its own, keyed by `user_id` with
     * an `expires_at` and a `created_ip`. Only a table shaped like the one Atom created
     * in 2014_10_12_100000 may be taken over - anything else belongs to the emulator and
     * is left untouched, tokens and all.
     */
    private function isAtomTable(string $table): bool
    {
        return Schema::hasTable($table)
            && Schema::hasColumns($table, ['email', 'token', 'created_at'])
            && ! Schema::hasColumn($table, 'user_id')
            && ! Schema::hasColumn($table, 'expires_at')
            && ! Schema::hasColumn($table, 'created_ip');
    }
};
