<?php

namespace App\Services\Auth;

use Closure;
use Illuminate\Support\Facades\DB;

final class RegistrationMutex
{
    /**
     * @template TResult
     *
     * @param  list<string>  $identifiers
     * @param  Closure(): TResult  $callback
     *
     * @return TResult
     */
    public function run(array $identifiers, Closure $callback): mixed
    {
        $keys = collect($identifiers)
            ->map(fn (string $identifier): string => hash('sha256', $identifier))
            ->unique()
            ->sort()
            ->values()
            ->all();

        return DB::transaction(function () use ($keys, $callback): mixed {
            foreach ($keys as $key) {
                DB::table('website_registration_locks')->insertOrIgnore([
                    'lock_key' => $key,
                ]);
            }

            foreach ($keys as $key) {
                DB::table('website_registration_locks')
                    ->where('lock_key', $key)
                    ->lockForUpdate()
                    ->value('lock_key');
            }

            try {
                return $callback();
            } finally {
                DB::table('website_registration_locks')
                    ->whereIn('lock_key', $keys)
                    ->delete();
            }
        }, attempts: 3);
    }
}
