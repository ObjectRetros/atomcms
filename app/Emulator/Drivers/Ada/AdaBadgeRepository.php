<?php

namespace App\Emulator\Drivers\Ada;

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Data\OwnedBadge;
use App\Models\Ada\AdaPlayerBadge;
use App\Models\User;
use App\Services\Badge\BadgeGrantMutex;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Ada splits badges over a shared definition table and a per-player table, so
 * a grant resolves (or creates) the definition first.
 *
 * Neither player_badges nor badges carries a unique constraint, so uniqueness
 * is enforced here rather than left to the database.
 */
class AdaBadgeRepository implements BadgeRepository
{
    public function __construct(private readonly BadgeGrantMutex $mutex = new BadgeGrantMutex) {}

    /** @return HasMany<AdaPlayerBadge, User> */
    public function relation(User $user): HasMany
    {
        return $user->hasMany(AdaPlayerBadge::class, 'player_id');
    }

    public function codes(User $user): array
    {
        return $this->badges($user)->pluck('badges.code')->filter()->values()->all();
    }

    public function grant(User $user, string $badge): void
    {
        // The code lock also serializes definitions shared by different
        // players and remains held through an outer purchase transaction.
        $this->mutex->run($badge, function () use ($user, $badge): void {
            if ($this->badges($user)->where('badges.code', $badge)->lockForUpdate()->exists()) {
                return;
            }

            DB::table('player_badges')->insert([
                'player_id' => $user->id,
                'badge_id' => $this->badgeId($badge),
                'slot' => 0,
            ]);
        });
    }

    public function revoke(User $user, string $badge): void
    {
        DB::table('player_badges')
            ->where('player_id', $user->id)
            ->whereIn('badge_id', DB::table('badges')->select('id')->where('code', $badge))
            ->delete();
    }

    public function paginate(User $user, int $perPage, string $pageName): LengthAwarePaginator
    {
        return $this->badges($user)
            ->orderByDesc('player_badges.id')
            ->paginate($perPage, ['badges.code', 'player_badges.slot'], $pageName)
            ->through(fn (object $row) => new OwnedBadge(
                (string) data_get($row, 'code'),
                (int) data_get($row, 'slot'),
            ));
    }

    /**
     * The definition id for a badge code, creating it when the emulator has
     * never seen the code. Ordered so a database that already holds duplicate
     * definitions keeps resolving to the same one.
     */
    private function badgeId(string $code): int
    {
        $existing = DB::table('badges')->where('code', $code)->orderBy('id')->lockForUpdate()->value('id');

        return (int) ($existing ?? DB::table('badges')->insertGetId(['code' => $code]));
    }

    private function badges(User $user): Builder
    {
        return DB::table('player_badges')
            ->join('badges', 'badges.id', '=', 'player_badges.badge_id')
            ->where('player_badges.player_id', $user->id);
    }
}
