<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        if (Schema::hasColumn('users', 'team_id')) {
            dropForeignKeyIfExists('users', 'team_id');
            Schema::dropColumns('users', 'team_id');
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable();
            }

            $table->foreign('team_id')->references('id')->on('website_teams')->nullOnDelete();
        });
    }
};
