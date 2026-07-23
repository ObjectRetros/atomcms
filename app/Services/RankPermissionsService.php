<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Shared shape for rank-gated permission lookups: a permission table is
 * cached for 30 minutes, memoized per instance and compared against a
 * user's rank. Subclasses only name the model and the cache key.
 */
abstract class RankPermissionsService
{
    /** @var Collection<string, int>|null */
    private ?Collection $permissions = null;

    /** @return class-string<Model> */
    abstract protected function model(): string;

    abstract protected function cacheKey(): string;

    /** @return Collection<string, int> */
    private function permissions(): Collection
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        $data = Cache::remember($this->cacheKey(), now()->addMinutes(30), function (): array {
            return $this->model()::query()->pluck('min_rank', 'permission')->toArray();
        });

        return $this->permissions = collect($data);
    }

    public function allows(User $user, string $permissionName, bool $default = false): bool
    {
        $permissions = $this->permissions();

        if (! $permissions->has($permissionName)) {
            return $default;
        }

        return $user->rank >= (int) $permissions->get($permissionName);
    }

    /**
     * Drop the persisted and memoized permissions so the next read refetches.
     * Instance-level, so objects already holding the singleton see fresh
     * values instead of a stale forgotten instance.
     */
    public function refresh(): void
    {
        Cache::forget($this->cacheKey());
        $this->permissions = null;
    }
}
