<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class HousekeepingPolicy
{
    use HandlesAuthorization;

    /**
     * Housekeeping permission required to view and manage the resource.
     */
    abstract protected function permission(): string;

    /**
     * Housekeeping permission required to destroy records. Defaults to the manage permission.
     */
    protected function deletePermission(): string
    {
        return $this->permission();
    }

    public function viewAny(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function view(User $user, mixed $record = null): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function create(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function update(User $user, mixed $record = null): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function reorder(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function replicate(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function attach(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function detach(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function detachAny(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function associate(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function dissociate(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function dissociateAny(User $user): bool
    {
        return $this->allowed($user, $this->permission());
    }

    public function delete(User $user, mixed $record = null): bool
    {
        return $this->allowed($user, $this->deletePermission());
    }

    public function deleteAny(User $user): bool
    {
        return $this->allowed($user, $this->deletePermission());
    }

    public function restore(User $user, mixed $record = null): bool
    {
        return $this->allowed($user, $this->deletePermission());
    }

    public function restoreAny(User $user): bool
    {
        return $this->allowed($user, $this->deletePermission());
    }

    public function forceDelete(User $user, mixed $record = null): bool
    {
        return $this->allowed($user, $this->deletePermission());
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->allowed($user, $this->deletePermission());
    }

    private function allowed(User $user, string $permission): bool
    {
        return hasHousekeepingPermission($permission, $user);
    }
}
