<?php

namespace App\Services\User;

use App\Emulator\Contracts\PlayerRepository;
use App\Models\User;
use App\Support\Sql;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class UserApiService
{
    public function __construct(private readonly PlayerRepository $players) {}

    public function fetchUser(string $username): ?User
    {
        return User::query()
            ->select(['username', 'motto', 'look'])
            ->where('username', $username)
            ->first();
    }

    /**
     * @param  array<int, string>  $columns
     *
     * @return Collection<int, User>
     */
    public function onlineUsers(array $columns = ['username', 'motto', 'look'], int $limit = 50): Collection
    {
        $cacheKey = sprintf('api_online_users:%s:%d', implode(',', $columns), $limit);

        return Cache::remember($cacheKey, now()->addSeconds(30), fn () => $this->players
            ->whereOnline(User::query())
            ->select($columns)
            ->inRandomOrder()
            ->limit($limit)
            ->get());
    }

    public function onlineUserCount(): int
    {
        return $this->players->whereOnline(User::query())->count();
    }

    /**
     * Prefix-search usernames, escaping LIKE wildcards so user input cannot
     * widen the match.
     *
     * @return array<int, array{username: string, look: string}>
     */
    public function searchUsers(string $query, int $limit = 8): array
    {
        return User::where('username', 'like', Sql::escapeLike($query) . '%')
            ->limit($limit)
            ->get(['username', 'look'])
            ->map(fn (User $user): array => [
                'username' => $user->username,
                'look' => $user->look,
            ])
            ->values()
            ->all();
    }
}
