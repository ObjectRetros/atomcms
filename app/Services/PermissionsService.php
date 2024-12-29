<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsitePermission;
use Illuminate\Support\Collection;

class PermissionsService
{
    public ?Collection $permissions;

    public function __construct()
    {
        $this->permissions = WebsitePermission::all()->pluck('min_rank', 'permission');
    }

    public function getOrDefault(string $permissionName, bool $default = false): bool
    {
        if (!array_key_exists($permissionName, $this->permissions->toArray())) {
            return $default;
        }

        return auth()->check() && auth()->user()->rank >= (int)$this->permissions->get($permissionName);
    }
}
