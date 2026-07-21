<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class UserApiService
{
    /** @param  list<string>  $columns */
    public function fetchUser(string $username, array $columns): ?User
    {
        return User::select($columns)->where('username', $username)->first();
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
}
