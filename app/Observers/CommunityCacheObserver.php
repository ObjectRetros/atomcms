<?php

namespace App\Observers;

use App\Models\Community\Teams\WebsiteTeam;
use App\Models\Game\Permission;
use App\Models\User;
use App\Support\CommunityCache;
use Illuminate\Database\Eloquent\Model;

class CommunityCacheObserver
{
    private const STAFF_PRESENTATION_ATTRIBUTES = [
        'hidden_staff',
        'look',
        'motto',
        'online',
        'rank',
        'username',
    ];

    private const TEAM_PRESENTATION_ATTRIBUTES = [
        'look',
        'motto',
        'online',
        'rank',
        'team_id',
        'username',
    ];

    public function created(Model $model): void
    {
        $this->forgetAffectedCaches($model, createdOrDeleted: true);
    }

    public function updated(Model $model): void
    {
        $this->forgetAffectedCaches($model, createdOrDeleted: false);
    }

    public function deleted(Model $model): void
    {
        $this->forgetAffectedCaches($model, createdOrDeleted: true);
    }

    private function forgetAffectedCaches(Model $model, bool $createdOrDeleted): void
    {
        if ($model instanceof User) {
            $isStaff = $model->rank >= (int) setting('min_staff_rank', 5);

            if (($createdOrDeleted && $isStaff) || (! $createdOrDeleted && $model->wasChanged(self::STAFF_PRESENTATION_ATTRIBUTES))) {
                CommunityCache::forgetStaffPositions();
            }

            if (($createdOrDeleted && $isStaff) || (! $createdOrDeleted && $model->wasChanged('rank'))) {
                CommunityCache::forgetStaffIds();
            }

            if (($createdOrDeleted && $model->team_id !== null) || (! $createdOrDeleted && $model->wasChanged(self::TEAM_PRESENTATION_ATTRIBUTES))) {
                CommunityCache::forgetTeams();
            }

            return;
        }

        if ($model instanceof Permission) {
            CommunityCache::forgetStaffPositions();
            CommunityCache::forgetTeams();

            return;
        }

        if ($model instanceof WebsiteTeam) {
            CommunityCache::forgetTeams();
        }
    }
}
