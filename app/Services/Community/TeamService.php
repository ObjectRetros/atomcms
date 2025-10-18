<?php

namespace App\Services\Community;

use App\Models\Community\Teams\WebsiteTeam;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TeamService
{
    public function fetchTeams(): Collection
    {
        $cacheEnabled = setting('enable_caching') === '1';

        if ($cacheEnabled && Cache::has('hotel_teams')) {
            return Cache::get('hotel_teams');
        }

        $employees = WebsiteTeam::select([
            'id',
            'rank_name',
            'badge',
            'staff_color',
            'staff_background',
            'job_description',
        ])
            ->where('hidden_rank', false)
            ->orderByDesc('id')
            ->with(['users' => function ($query) {
                $query->select('id', 'username', 'look', 'motto', 'rank', 'team_id', 'online');
            }])
            ->get();

        if ($cacheEnabled) {
            $cacheTimer = (int) setting('cache_timer');
            Cache::put('hotel_teams', $employees, now()->addMinutes($cacheTimer));
        }

        return $employees;
    }
}
