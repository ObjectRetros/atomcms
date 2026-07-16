<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('website_staff_applications', 'status')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->after('content');
            });
        }

        if (! Schema::hasColumn('website_staff_applications', 'approved_by')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->integer('approved_by')->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('website_staff_applications', 'approved_at')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            });
        }

        if (! Schema::hasColumn('website_staff_applications', 'rejected_by')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->integer('rejected_by')->nullable()->after('approved_at');
            });
        }

        if (! Schema::hasColumn('website_staff_applications', 'rejected_at')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            });
        }

        if (! $this->foreignKeyExists('wsa_approved_by_fk')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->foreign('approved_by', 'wsa_approved_by_fk')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            });
        }

        if (! $this->foreignKeyExists('wsa_rejected_by_fk')) {
            Schema::table('website_staff_applications', function (Blueprint $table) {
                $table->foreign('rejected_by', 'wsa_rejected_by_fk')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['wsa_approved_by_fk', 'wsa_rejected_by_fk'] as $foreignKey) {
            if ($this->foreignKeyExists($foreignKey)) {
                Schema::table('website_staff_applications', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey);
                });
            }
        }

        Schema::table('website_staff_applications', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'rejected_by', 'approved_at', 'approved_by', 'status']);
        });
    }

    private function foreignKeyExists(string $constraint): bool
    {
        $result = DB::selectOne(<<<'SQL'
            SELECT COUNT(*) AS aggregate
            FROM information_schema.table_constraints
            WHERE constraint_schema = ?
                AND table_name = 'website_staff_applications'
                AND constraint_name = ?
                AND constraint_type = 'FOREIGN KEY'
            SQL, [DB::getDatabaseName(), $constraint]);

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
