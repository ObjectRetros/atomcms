<?php

namespace App\Services;

use App\Models\User;
use App\Models\WebsiteHousekeepingPermission;

class HousekeepingPermissionsService extends RankPermissionsService
{
    protected function model(): string
    {
        return WebsiteHousekeepingPermission::class;
    }

    protected function cacheKey(): string
    {
        return 'housekeeping_permissions';
    }

    public function getOrDefault(string $permissionName, bool $default = false, ?User $user = null): bool
    {
        if ($user === null) {
            $authenticatedUser = auth()->user();
            $user = $authenticatedUser instanceof User ? $authenticatedUser : null;
        }

        return $user instanceof User
            ? $this->allows($user, $permissionName, $default)
            : $default;
    }
}
