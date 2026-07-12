<?php

namespace App\Emulator\Drivers\Plus;

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Data\OwnedBadge;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Plus EMU stores owned badges in user_badges (user_id, badge_id, badge_slot),
 * where badge_id holds the badge code.
 */
class PlusBadgeRepository implements BadgeRepository
{
    public function codes(User $user): array
    {
        return $this->badges($user)->pluck('badge_id')->all();
    }

    public function grant(User $user, string $badge): void
    {
        if ($this->badges($user)->where('badge_id', $badge)->exists()) {
            return;
        }

        $this->table()->insert([
            'user_id' => $user->id,
            'badge_id' => $badge,
            'badge_slot' => 0,
        ]);
    }

    public function revoke(User $user, string $badge): void
    {
        $this->badges($user)->where('badge_id', $badge)->delete();
    }

    public function paginate(User $user, int $perPage, string $pageName): LengthAwarePaginator
    {
        return $this->badges($user)
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], $pageName)
            ->through(fn (object $row) => new OwnedBadge($row->badge_id, (int) $row->badge_slot));
    }

    private function badges(User $user): Builder
    {
        return $this->table()->where('user_id', $user->id);
    }

    private function table(): Builder
    {
        return DB::table('user_badges');
    }
}
