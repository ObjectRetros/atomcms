<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsitePermission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PermissionsService
{
    /** @var Collection<string, int>|null */
    private ?Collection $permissions = null;

    private function permissions(): Collection
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        $data = Cache::remember('website_permissions', now()->addMinutes(30), function (): array {
            return WebsitePermission::all()->pluck('min_rank', 'permission')->toArray();
        });

        return $this->permissions = collect($data);
    }

    public function getOrDefault(string $permissionName, bool $default = false): bool
    {
        $user = auth()->user();

        return $user instanceof User
            ? $this->allows($user, $permissionName, $default)
            : $default;
    }

    public function allows(User $user, string $permissionName, bool $default = false): bool
    {
        $permissions = $this->permissions();

        if (! $permissions->has($permissionName)) {
            return $default;
        }

        return $user->rank >= (int) $permissions->get($permissionName);
    }

    public static function clearCache(): void
    {
        Cache::forget('website_permissions');
        app()->forgetInstance(self::class);
    }
}
