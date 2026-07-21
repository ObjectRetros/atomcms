<?php

namespace App\Services\Community;

use App\Models\Game\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StaffService
{
    /** @return Collection<int, Permission> */
    public function fetchStaffPositions(): Collection
    {
        $cacheEnabled = setting('enable_caching') === '1';
        $viewer = Auth::user();
        $canSeeHiddenStaff = $viewer instanceof User
            && $viewer->rank >= (int) setting('min_rank_to_see_hidden_staff');
        $cacheKey = $canSeeHiddenStaff ? 'staff_positions.all' : 'staff_positions.public';

        if (! $cacheEnabled) {
            return $this->staffPositions($canSeeHiddenStaff);
        }

        return Cache::remember(
            $cacheKey,
            now()->addMinutes((int) setting('cache_timer')),
            fn (): Collection => $this->staffPositions($canSeeHiddenStaff),
        );
    }

    /** @return Collection<int, Permission> */
    private function staffPositions(bool $canSeeHiddenStaff): Collection
    {
        return Permission::query()
            ->select('id', 'rank_name', 'badge', 'staff_color', 'job_description')
            ->when(! $canSeeHiddenStaff, function ($query) {
                return $query->where('hidden_rank', false);
            })
            ->where('id', '>=', setting('min_staff_rank'))
            ->orderByDesc('id')
            ->with(['users' => function ($query) use ($canSeeHiddenStaff) {
                $query->select('id', 'username', 'rank', 'motto', 'look', 'hidden_staff', 'online')
                    ->when(! $canSeeHiddenStaff, function ($query) {
                        return $query->where('hidden_staff', false);
                    })
                    ->with('permission:id,rank_name,staff_background');
            }])
            ->get();
    }

    /** @return list<int> */
    public function fetchEmployeeIds(): array
    {
        $cacheEnabled = setting('enable_caching') === '1';

        if ($cacheEnabled && Cache::has('staff_ids')) {
            return Cache::get('staff_ids');
        }

        $staffIds = array_values(User::select('id')
            ->where('rank', '>=', setting('min_staff_rank'))
            ->get()
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->values()
            ->all());

        if ($cacheEnabled) {
            $cacheTimer = (int) setting('cache_timer');
            Cache::put('staff_ids', $staffIds, now()->addMinutes($cacheTimer));
        }

        return $staffIds;
    }
}
