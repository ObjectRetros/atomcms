<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'edit_user';
    }

    protected function deletePermission(): string
    {
        return 'delete_user';
    }

    public function view(User $user, mixed $record = null): bool
    {
        return parent::view($user, $record) && $this->canManage($user, $record);
    }

    public function update(User $user, mixed $record = null): bool
    {
        return parent::update($user, $record) && $this->canManage($user, $record);
    }

    public function delete(User $user, mixed $record = null): bool
    {
        return parent::delete($user, $record) && $this->canManage($user, $record);
    }

    public function restore(User $user, mixed $record = null): bool
    {
        return parent::restore($user, $record) && $this->canManage($user, $record);
    }

    public function forceDelete(User $user, mixed $record = null): bool
    {
        return parent::forceDelete($user, $record) && $this->canManage($user, $record);
    }

    private function canManage(User $actor, mixed $record): bool
    {
        return $record instanceof User
            && ! $actor->is($record)
            && $actor->rank > $record->rank;
    }
}
