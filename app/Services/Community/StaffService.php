<?php

namespace App\Services\Community;

use App\Models\Game\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StaffService
{
    public function fetchStaffPositions(): Collection
    {
        $cacheEnabled = setting('enable_caching') === '1';

        if ($cacheEnabled && Cache::has('staff_positions')) {
            return Cache::get('staff_positions');
        }

        $employees = Permission::query()
            ->select('id', 'rank_name', 'badge', 'staff_color', 'job_description')
            ->when(Auth::user()->rank < (int) setting('min_rank_to_see_hidden_staff'), function ($query) {
                return $query->where('hidden_rank', false);
            })
            ->where('id', '>=', setting('min_staff_rank'))
            ->orderByDesc('id')
            ->with(['users' => function ($query) {
                $query->select('id', 'username', 'rank', 'look', 'hidden_staff', 'online')
                    ->when(Auth::user()->rank < (int) setting('min_rank_to_see_hidden_staff'), function ($query) {
                        return $query->where('hidden_staff', false);
                    });
            }])
            ->get();

        if ($cacheEnabled) {
            $cacheTimer = (int) setting('cache_timer');
            Cache::put('staff_positions', $employees, now()->addMinutes($cacheTimer));
        }

        return $employees;
    }

    public function fetchEmployeeIds(): array
    {
        $cacheEnabled = setting('enable_caching') === '1';

        if ($cacheEnabled && Cache::has('staff_ids')) {
            return Cache::get('staff_ids');
        }

        $staffIds = User::select('id')
            ->where('rank', '>=', setting('min_staff_rank'))
            ->get()
            ->pluck('id')->toArray();

        if ($cacheEnabled) {
            $cacheTimer = (int) setting('cache_timer');
            Cache::put('staff_ids', $staffIds, now()->addMinutes($cacheTimer));
        }

        return $staffIds;
    }
}
