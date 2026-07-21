<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->foreignKeyExists('website_open_positions', 'website_open_positions_team_id_foreign')) {
            Schema::table('website_open_positions', function (Blueprint $table) {
                $table->foreign('team_id', 'website_open_positions_team_id_foreign')
                    ->references('id')->on('website_teams')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if ($this->foreignKeyExists('website_open_positions', 'website_open_positions_team_id_foreign')) {
            Schema::table('website_open_positions', function (Blueprint $table) {
                $table->dropForeign('website_open_positions_team_id_foreign');
            });
        }
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $db = DB::getDatabaseName();
        $row = DB::selectOne("
            SELECT COUNT(*) as cnt
            FROM information_schema.table_constraints
            WHERE constraint_schema = ? AND table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY'
        ", [$db, $table, $constraint]);

        return (int) ($row->cnt ?? 0) > 0;
    }
};
