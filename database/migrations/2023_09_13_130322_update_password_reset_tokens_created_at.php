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
        // Absent on databases where an emulator owned `password_resets` and Atom
        // therefore never created its own table - see 2026_08_14_000000.
        if (! Schema::hasTable('password_reset_tokens')) {
            return;
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
