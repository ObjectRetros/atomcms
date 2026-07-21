<?php

namespace App\Services\Community;

use App\Models\Community\Teams\WebsiteTeam;
use App\Support\CommunityCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TeamService
{
    /** @return Collection<int, WebsiteTeam> */
    public function fetchTeams(): Collection
    {
        $cacheEnabled = setting('enable_caching') === '1';

        $resolve = fn (): Collection => WebsiteTeam::select([
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
                $query->select('id', 'username', 'look', 'motto', 'rank', 'team_id', 'online')
                    ->with('permission:id,rank_name,staff_background');
            }])
            ->get();

        if (! $cacheEnabled) {
            return $resolve();
        }

        return Cache::remember(
            CommunityCache::TEAMS,
            now()->addMinutes((int) setting('cache_timer')),
            $resolve,
        );
    }
}
