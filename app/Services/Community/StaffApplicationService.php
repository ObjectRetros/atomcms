<?php

namespace App\Services\Community;

use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class StaffApplicationService
{
    public function storeApplication(User $user, int $positionId, string $content): void
    {
        $user->applications()->create([
            'rank_id' => $positionId,
            'content' => $content,
        ]);
    }

    public function fetchOpenPositions(): Collection
    {
        return WebsiteOpenPosition::canApply()->with('permission')->get();
    }

    public function hasUserAppliedForPosition($user, $positionId): bool
    {
        return $user->applications()->where('rank_id', $positionId)->exists();
    }

    public function isPositionOpenForApplication($position): bool
    {
        $currentTime = now();

        return $position->apply_from <= $currentTime && $position->apply_to >= $currentTime;
    }
}
