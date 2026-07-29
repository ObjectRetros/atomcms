<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\PlayerRepository;
use App\Emulator\Data\HomeFriend;
use App\Models\Game\Player\MessengerFriendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Arcturus keeps the CMS and emulator identity in the same users row, so
 * there is nothing to synchronise or hydrate - the row is already live.
 */
class ArcturusPlayerRepository implements PlayerRepository
{
    public function created(User $user): void {}

    public function updated(User $user): void {}

    public function deleted(User $user): void {}

    public function hydrateMany(array $users): void {}

    public function whereOnline(Builder $query): Builder
    {
        return $query->where($query->getModel()->qualifyColumn('online'), '1');
    }

    public function issueSso(User $user): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $sso = sprintf('%s-%s', Str::replace(' ', '', setting('hotel_name', 'Atom')), Str::uuid());

            if (! User::where('auth_ticket', $sso)->exists()) {
                // auth_ticket is guarded against mass assignment, so this
                // trusted path has to write it explicitly.
                $user->forceFill(['auth_ticket' => $sso])->save();

                return $sso;
            }
        }

        throw new \RuntimeException('Failed to generate unique SSO ticket after 5 attempts.');
    }

    public function onlineFriends(User $user, int $limit): Collection
    {
        return $this->whereOnline(
            User::query()->whereIn(
                'users.id',
                MessengerFriendship::query()->where('user_one_id', $user->id)->select('user_two_id'),
            ),
        )
            // Ordered by recency rather than at random: inRandomOrder()
            // makes the server sort the whole matching set on every
            // profile view, and the widget only shows a handful.
            ->orderByDesc('users.last_online')
            ->limit($limit)
            ->get(['users.id', 'users.username', 'users.look', 'users.motto', 'users.last_online']);
    }

    /** @return LengthAwarePaginator<int, HomeFriend> */
    public function friendsForHome(User $user, int $perPage, string $pageName): LengthAwarePaginator
    {
        $paginator = MessengerFriendship::query()
            ->where('user_one_id', $user->id)
            ->select('user_two_id')
            ->with('user:id,username,look,online')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], $pageName);

        $friends = $paginator->getCollection()
            ->map(fn (MessengerFriendship $friendship): HomeFriend => new HomeFriend($friendship->user))
            ->values();

        return new LengthAwarePaginator($friends, $paginator->total(), $perPage, $paginator->currentPage(), [
            'path' => $paginator->path(),
            'pageName' => $pageName,
        ]);
    }
}
