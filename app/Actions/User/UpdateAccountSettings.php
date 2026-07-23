<?php

namespace App\Actions\User;

use App\Contracts\Rcon;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UpdateAccountSettings
{
    public function __construct(private readonly Rcon $rcon) {}

    /**
     * @param  array{username?: string, mail: string, motto?: string|null}  $data
     *
     * @throws ValidationException
     */
    public function execute(User $user, array $data): void
    {
        if (! $this->rcon->isConnected() && $user->online) {
            throw ValidationException::withMessages([
                'account' => __('You must be offline to change your account settings'),
            ]);
        }

        if ($user->mail !== $data['mail']) {
            $user->update(['mail' => $data['mail']]);
        }

        // The motto is nullable in validation; clearing it means an empty string.
        $motto = (string) ($data['motto'] ?? '');

        if ($user->motto !== $motto) {
            $this->rcon->setMotto($user, $motto);
            $user->update(['motto' => $motto]);
        }

        if (array_key_exists('username', $data)) {
            $this->renameUser($user, $data['username']);
        }
    }

    /** @throws ValidationException */
    private function renameUser(User $user, string $username): void
    {
        if ($user->username === $username) {
            return;
        }

        // The emulator grants a single rename by flipping allow_name_change to
        // '1' (users_settings); consume the grant again after a successful
        // rename, mirroring the in-game flow.
        if (! $user->settings?->allow_name_change) {
            throw ValidationException::withMessages([
                'username' => __('You are not allowed to change your username'),
            ]);
        }

        $user->update(['username' => $username]);
        $user->settings->update(['allow_name_change' => '0']);
    }
}
