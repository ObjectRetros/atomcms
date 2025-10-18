<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_open_positions', function (Blueprint $table) {
            if (! Schema::hasColumn('website_open_positions', 'position_kind')) {
                $table->string('position_kind', 20)->default('rank')->after('id');
            }

            if (! Schema::hasColumn('website_open_positions', 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable()->after('permission_id');
            }
        });

        // description -> TEXT (no doctrine/dbal)
        DB::statement('ALTER TABLE website_open_positions MODIFY description TEXT NOT NULL');

        // Unique indexes (allow multiple NULLs)
        if (! $this->indexExists('website_open_positions', 'open_positions_unique_permission_id')) {
            Schema::table('website_open_positions', function (Blueprint $table) {
                $table->unique('permission_id', 'open_positions_unique_permission_id');
            });
        }
        if (! $this->indexExists('website_open_positions', 'open_positions_unique_team_id')) {
            Schema::table('website_open_positions', function (Blueprint $table) {
                $table->unique('team_id', 'open_positions_unique_team_id');
            });
        }

        // Optional FK to teams
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
        if ($this->indexExists('website_open_positions', 'open_positions_unique_permission_id')) {
            Schema::table('website_open_positions', function (Blueprint $table) {
                $table->dropUnique('open_positions_unique_permission_id');
            });
        }
        if ($this->indexExists('website_open_positions', 'open_positions_unique_team_id')) {
            Schema::table('website_open_positions', function (Blueprint $table) {
                $table->dropUnique('open_positions_unique_team_id');
            });
        }

        if ($this->foreignKeyExists('website_open_positions', 'website_open_positions_team_id_foreign')) {
            Schema::table('website_open_positions', function (Blueprint $table) {
                $table->dropForeign('website_open_positions_team_id_foreign');
            });
        }

        DB::statement('ALTER TABLE website_open_positions MODIFY description VARCHAR(255) NOT NULL');

        Schema::table('website_open_positions', function (Blueprint $table) {
            if (Schema::hasColumn('website_open_positions', 'team_id')) {
                $table->dropColumn('team_id');
            }
            if (Schema::hasColumn('website_open_positions', 'position_kind')) {
                $table->dropColumn('position_kind');
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $db = DB::getDatabaseName();
        $row = DB::selectOne('
            SELECT COUNT(*) as cnt
            FROM information_schema.statistics
            WHERE table_schema = ? AND table_name = ? AND index_name = ?
        ', [$db, $table, $index]);

        return (int) ($row->cnt ?? 0) > 0;
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
