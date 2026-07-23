<?php

namespace App\Observers;

use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;

class WebsiteOpenPositionObserver
{
    /**
     * Cascades the applications submitted for a position when it is removed.
     *
     * A database-level cascade is not possible here: applications reference
     * permissions (rank_id) and teams (team_id), never the position row
     * itself, so the cleanup has to happen in the deletion path.
     */
    public function deleting(WebsiteOpenPosition $openPosition): void
    {
        if ($openPosition->position_kind === 'rank' && $openPosition->permission_id) {
            WebsiteStaffApplications::query()->where('rank_id', $openPosition->permission_id)->delete();
        }
    }
}
