<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Data\OwnedBadge;
use App\Models\Game\Player\UserBadge;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Arcturus stores owned badges in users_badges (user_id, slot_id, badge_code).
 */
class ArcturusBadgeRepository implements BadgeRepository
{
    public function codes(User $user): array
    {
        return $user->badges()->pluck('badge_code')->all();
    }

    public function grant(User $user, string $badge): void
    {
        if ($user->badges()->where('badge_code', $badge)->exists()) {
            return;
        }

        $user->badges()->create([
            'slot_id' => 0,
            'badge_code' => $badge,
        ]);
    }

    public function revoke(User $user, string $badge): void
    {
        $user->badges()->where('badge_code', $badge)->delete();
    }

    public function paginate(User $user, int $perPage, string $pageName): LengthAwarePaginator
    {
        return $user->badges()
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], $pageName)
            ->through(fn (UserBadge $row) => new OwnedBadge($row->badge_code, (int) $row->slot_id));
    }
}
