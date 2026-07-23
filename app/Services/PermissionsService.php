<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsitePermission;
use App\Models\User;

class PermissionsService extends RankPermissionsService
{
    protected function model(): string
    {
        return WebsitePermission::class;
    }

    protected function cacheKey(): string
    {
        return 'website_permissions';
    }

    public function getOrDefault(string $permissionName, bool $default = false): bool
    {
        $user = auth()->user();

        return $user instanceof User
            ? $this->allows($user, $permissionName, $default)
            : $default;
    }
}
