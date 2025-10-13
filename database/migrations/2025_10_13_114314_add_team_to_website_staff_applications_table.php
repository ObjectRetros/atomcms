<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_staff_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('website_staff_applications', 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable()->after('rank_id');
            }
        });

        DB::statement('ALTER TABLE website_staff_applications MODIFY rank_id INT(11) NULL');

        if (! $this->foreignKeyExists('website_staff_applications', 'website_staff_applications_team_id_foreign')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->foreign('team_id', 'website_staff_applications_team_id_foreign')
                    ->references('id')->on('website_teams')
                    ->nullOnDelete();
            });
        }

        if (! $this->indexExists('website_staff_applications', 'website_staff_applications_user_team_unique')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->unique(['user_id', 'team_id'], 'website_staff_applications_user_team_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('website_staff_applications', 'website_staff_applications_user_team_unique')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->dropUnique('website_staff_applications_user_team_unique');
            });
        }

        if ($this->foreignKeyExists('website_staff_applications', 'website_staff_applications_team_id_foreign')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->dropForeign('website_staff_applications_team_id_foreign');
            });
        }

        DB::statement('ALTER TABLE website_staff_applications MODIFY rank_id INT(11) NOT NULL');

        if (Schema::hasColumn('website_staff_applications', 'team_id')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->dropColumn('team_id');
            });
        }
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
