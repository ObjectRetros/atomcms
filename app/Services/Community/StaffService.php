<?php

namespace App\Services\Community;

use App\Models\Game\Permission;
use App\Models\User;
use App\Support\CommunityCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class StaffService
{
    /** @return Collection<int, Permission> */
    public function fetchStaffPositions(User $viewer): Collection
    {
        $cacheEnabled = setting('enable_caching') === '1';
        $includeHidden = $viewer->rank >= (int) setting('min_rank_to_see_hidden_staff');
        $resolve = fn (): Collection => Permission::query()
            ->select('id', 'rank_name', 'badge', 'staff_color', 'job_description')
            ->when(! $includeHidden, fn ($query) => $query->where('hidden_rank', false))
            ->where('id', '>=', setting('min_staff_rank'))
            ->orderByDesc('id')
            ->with(['users' => function ($query) use ($includeHidden) {
                $query->select('id', 'username', 'rank', 'motto', 'look', 'hidden_staff', 'online')
                    ->when(! $includeHidden, fn ($query) => $query->where('hidden_staff', false))
                    ->with('permission:id,rank_name,staff_background');
            }])
            ->get();

        if (! $cacheEnabled) {
            return $resolve();
        }

        return Cache::remember(
            CommunityCache::staffPositionsKey($includeHidden),
            now()->addMinutes((int) setting('cache_timer')),
            $resolve,
        );
    }

    /** @return list<int> */
    public function fetchEmployeeIds(): array
    {
        $cacheEnabled = setting('enable_caching') === '1';

        $resolve = fn (): array => User::select('id')
            ->where('rank', '>=', setting('min_staff_rank'))
            ->get()
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->values()
            ->all();

        $ids = $cacheEnabled ? Cache::remember(
            CommunityCache::STAFF_IDS,
            now()->addMinutes((int) setting('cache_timer')),
            $resolve,
        ) : $resolve();

        return array_values($ids);
    }
}
