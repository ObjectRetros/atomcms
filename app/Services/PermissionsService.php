<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsitePermission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PermissionsService
{
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
        $permissions = $this->permissions();

        if (! $permissions->has($permissionName)) {
            return $default;
        }

        return auth()->check() && auth()->user()->rank >= (int) $permissions->get($permissionName);
    }

    public static function clearCache(): void
    {
        Cache::forget('website_permissions');
        app()->forgetInstance(self::class);
    }
}
