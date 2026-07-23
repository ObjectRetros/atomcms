<?php

namespace App\Services\User;

use App\Models\User;
use App\Support\Sql;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class UserApiService
{
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

        return Cache::remember($cacheKey, now()->addSeconds(30), fn () => User::query()
            ->select($columns)
            ->where('online', '1')
            ->inRandomOrder()
            ->limit($limit)
            ->get());
    }

    public function onlineUserCount(): int
    {
        return User::where('online', '1')->count();
    }

    /**
     * Prefix-search usernames, escaping LIKE wildcards so user input cannot
     * widen the match.
     *
     * @return list<array{username: string, look: string|null}>
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
