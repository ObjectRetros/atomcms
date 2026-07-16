<?php

namespace App\Services;

use App\Models\User;
use App\Models\WebsiteHousekeepingPermission;
use Illuminate\Support\Collection;

class HousekeepingPermissionsService
{
    /** @var Collection<string, int> */
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

        $user = auth()->user();

        return $user instanceof User
            && $user->rank >= (int) $this->permissions->get($permissionName);
    }
}
