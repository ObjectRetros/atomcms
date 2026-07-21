<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_registration_locks', function (Blueprint $table) {
            $table->string('lock_key', 64)->primary();
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasIndex('users', ['ip_register'])) {
                $table->index('ip_register', 'users_ip_register_registration_index');
            }

            if (! Schema::hasIndex('users', ['ip_current'])) {
                $table->index('ip_current', 'users_ip_current_registration_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'users_ip_register_registration_index')) {
                $table->dropIndex('users_ip_register_registration_index');
            }

            if (Schema::hasIndex('users', 'users_ip_current_registration_index')) {
                $table->dropIndex('users_ip_current_registration_index');
            }
        });

        Schema::dropIfExists('website_registration_locks');
    }
};
