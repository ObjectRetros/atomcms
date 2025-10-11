<?php

namespace App\Actions;

class UserActions
{
    public function updateUsername($user, $username): void
    {
        $user->update([
            'username' => $username,
        ]);
    }

    public function updateEmail($user, $email): void
    {
        $user->update([
            'mail' => $email,
        ]);
    }

    public function updateMotto($user, $motto): void
    {
        $user->update([
            'motto' => $motto,
        ]);
    }

    public function updateField($user, string $field, ?string $value): void
    {
        $user->update([
            $field => $value,
        ]);
    }
}
