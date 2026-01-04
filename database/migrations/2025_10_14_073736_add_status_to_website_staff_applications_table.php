<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE website_staff_applications MODIFY rank_id INT(11) NULL');

        if (! Schema::hasColumn('website_staff_applications', 'team_id')) {
            DB::statement('ALTER TABLE website_staff_applications ADD team_id INT(11) NULL AFTER rank_id');
        }

        if (! Schema::hasColumn('website_staff_applications', 'status')) {
            DB::statement("ALTER TABLE website_staff_applications ADD status VARCHAR(20) NOT NULL DEFAULT 'pending' AFTER content");
        }

        if (! Schema::hasColumn('website_staff_applications', 'approved_by')) {
            DB::statement('ALTER TABLE website_staff_applications ADD approved_by INT UNSIGNED NULL AFTER status');
        }
        if (! Schema::hasColumn('website_staff_applications', 'approved_at')) {
            DB::statement('ALTER TABLE website_staff_applications ADD approved_at TIMESTAMP NULL AFTER approved_by');
        }
        if (! Schema::hasColumn('website_staff_applications', 'rejected_by')) {
            DB::statement('ALTER TABLE website_staff_applications ADD rejected_by INT UNSIGNED NULL AFTER approved_at');
        }
        if (! Schema::hasColumn('website_staff_applications', 'rejected_at')) {
            DB::statement('ALTER TABLE website_staff_applications ADD rejected_at TIMESTAMP NULL AFTER rejected_by');
        }

        try {
            DB::statement('ALTER TABLE website_staff_applications
                ADD CONSTRAINT wsa_team_id_fk
                FOREIGN KEY (team_id) REFERENCES website_teams(id)
                ON DELETE SET NULL');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('ALTER TABLE website_staff_applications
                ADD CONSTRAINT wsa_approved_by_fk
                FOREIGN KEY (approved_by) REFERENCES users(id)
                ON DELETE SET NULL');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('ALTER TABLE website_staff_applications
                ADD CONSTRAINT wsa_rejected_by_fk
                FOREIGN KEY (rejected_by) REFERENCES users(id)
                ON DELETE SET NULL');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('CREATE UNIQUE INDEX wsa_user_team_unique
                ON website_staff_applications (user_id, team_id)');
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        try {
            DB::statement('DROP INDEX wsa_user_team_unique ON website_staff_applications');
        } catch (\Throwable $e) {
        }

        foreach (['wsa_team_id_fk', 'wsa_approved_by_fk', 'wsa_rejected_by_fk'] as $fk) {
            try {
                DB::statement("ALTER TABLE website_staff_applications DROP FOREIGN KEY {$fk}");
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasColumn('website_staff_applications', 'rejected_at')) {
            DB::statement('ALTER TABLE website_staff_applications DROP COLUMN rejected_at');
        }
        if (Schema::hasColumn('website_staff_applications', 'rejected_by')) {
            DB::statement('ALTER TABLE website_staff_applications DROP COLUMN rejected_by');
        }
        if (Schema::hasColumn('website_staff_applications', 'approved_at')) {
            DB::statement('ALTER TABLE website_staff_applications DROP COLUMN approved_at');
        }
        if (Schema::hasColumn('website_staff_applications', 'approved_by')) {
            DB::statement('ALTER TABLE website_staff_applications DROP COLUMN approved_by');
        }
        if (Schema::hasColumn('website_staff_applications', 'status')) {
            DB::statement('ALTER TABLE website_staff_applications DROP COLUMN status');
        }
        if (Schema::hasColumn('website_staff_applications', 'team_id')) {
            DB::statement('ALTER TABLE website_staff_applications DROP COLUMN team_id');
        }

        DB::statement('ALTER TABLE website_staff_applications MODIFY rank_id INT(11) NOT NULL');
    }
};
