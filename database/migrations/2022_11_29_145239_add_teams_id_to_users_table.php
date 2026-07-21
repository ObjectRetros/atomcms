<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'team_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('team_id')->nullable();
            });
        }

        dropForeignKeyIfExists('users', 'team_id');

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('website_teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        dropForeignKeyIfExists('users', 'team_id');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
};
