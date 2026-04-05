<?php

namespace App\Services;

use App\Models\WebsiteHousekeepingPermission;
use Illuminate\Support\Collection;

class HousekeepingPermissionsService
{
    public Collection $permissions;

    public function __construct()
    {
        $this->permissions = WebsiteHousekeepingPermission::all()->pluck('min_rank', 'permission');
    }

    public function getOrDefault(string $permissionName, bool $default = false): bool
    {
        if (! $this->permissions->has($permissionName)) {
            return $default;
        }

        return auth()->check() && auth()->user()->rank >= (int) $this->permissions->get($permissionName);
    }
}
