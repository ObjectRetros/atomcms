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
            $table->string('application_key')->nullable()->after('id');
        });

        $this->assignCanonicalKeys('rank', 'rank_id', 'team_id');
        $this->assignCanonicalKeys('team', 'team_id', 'rank_id');

        Schema::table('website_staff_applications', function (Blueprint $table) {
            $table->unique('application_key', 'staff_applications_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('website_staff_applications', function (Blueprint $table) {
            $table->dropUnique('staff_applications_key_unique');
            $table->dropColumn('application_key');
        });
    }

    private function assignCanonicalKeys(string $kind, string $targetColumn, string $otherTargetColumn): void
    {
        $seen = [];

        DB::table('website_staff_applications')
            ->select(['id', 'user_id', $targetColumn])
            ->whereNotNull($targetColumn)
            ->whereNull($otherTargetColumn)
            ->orderBy('id')
            ->chunkById(500, function ($applications) use ($kind, $targetColumn, &$seen): void {
                foreach ($applications as $application) {
                    $key = sprintf(
                        '%s:%d:%d',
                        $kind,
                        $application->user_id,
                        $application->{$targetColumn},
                    );

                    if (isset($seen[$key])) {
                        continue;
                    }

                    DB::table('website_staff_applications')
                        ->where('id', $application->id)
                        ->update(['application_key' => $key]);

                    $seen[$key] = true;
                }
            });
    }
};
