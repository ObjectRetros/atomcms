<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_open_positions', function (Blueprint $table) {
            $table->string('position_kind')->default('rank')->after('id');
            $table->unsignedBigInteger('team_id')->nullable()->after('permission_id');
            $table->unique('permission_id', 'open_positions_unique_permission_id');
            $table->unique('team_id', 'open_positions_unique_team_id');
        });
    }

    public function down(): void
    {
        Schema::table('website_open_positions', function (Blueprint $table) {
            $table->dropUnique('open_positions_unique_permission_id');
            $table->dropUnique('open_positions_unique_team_id');
            $table->dropColumn(['position_kind', 'team_id']);
        });
    }
};
