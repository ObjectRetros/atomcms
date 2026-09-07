<?php

namespace App\Services\Badge;

use Closure;
use Illuminate\Support\Facades\DB;

final readonly class BadgeGrantMutex
{
    /**
     * Callers that own an outer transaction must retry concurrency errors
     * there, since a nested savepoint cannot refresh its read snapshot.
     *
     * @param  Closure(): void  $grant
     */
    public function run(string $code, Closure $grant): void
    {
        $key = hash('sha256', strtolower($code));

        DB::transaction(function () use ($key, $grant): void {
            DB::table('website_badge_grant_locks')->insertOrIgnore(['lock_key' => $key]);
            DB::table('website_badge_grant_locks')->where('lock_key', $key)->lockForUpdate()->first();

            try {
                $grant();
            } finally {
                // The database retains this lock until the outermost commit
                // or rollback, without accumulating one mutex row per code.
                DB::table('website_badge_grant_locks')->where('lock_key', $key)->delete();
            }
        }, attempts: 3);
    }
}
