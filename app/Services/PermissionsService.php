<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsitePermission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PermissionsService
{
    /** @var Collection<string, int> */
    public Collection $permissions;

    public function __construct()
    {
        $data = Cache::remember('website_permissions', now()->addMinutes(30), function () {
            return WebsitePermission::all()->pluck('min_rank', 'permission')->toArray();
        });

        $this->permissions = collect($data);
    }

    public function getOrDefault(string $permissionName, bool $default = false): bool
    {
        if (! $this->permissions->has($permissionName)) {
            return $default;
        }

        $user = auth()->user();

        return $user instanceof User
            && $user->rank >= (int) $this->permissions->get($permissionName);
    }
}
