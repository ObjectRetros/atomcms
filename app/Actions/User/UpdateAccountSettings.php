<?php

namespace App\Actions\User;

use App\Contracts\Rcon;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateAccountSettings
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly PlayerSettingsRepository $settings,
    ) {}

    /**
     * @param  array<string, mixed>  $data  The validated AccountSettingsFormRequest payload.
     *
     * @throws ValidationException
     */
    public function execute(User $user, array $data): void
    {
        // Probed once: a driver with no RCON bridge answers false, and an
        // online player on such a driver has to sign out before editing.
        $connected = $this->rcon->isConnected();

        if (! $connected && $user->online) {
            throw ValidationException::withMessages([
                'account' => __('You must be offline to change your account settings'),
            ]);
        }

        $mail = is_string($data['mail'] ?? null) ? $data['mail'] : null;

        // The motto is nullable in validation; clearing it means an empty string.
        $motto = is_string($data['motto'] ?? null) ? $data['motto'] : '';

        // Both fields move together, so a failure part way through cannot leave
        // the account with only one of them changed.
        DB::transaction(function () use ($user, $mail, $motto, $connected): void {
            $changes = [];

            if ($mail !== null && $user->mail !== $mail) {
                $changes['mail'] = $mail;
            }

            if ($user->motto !== $motto) {
                $changes['motto'] = $motto;

                // Only a live emulator needs telling. Offline, and on drivers
                // with no RCON bridge at all, the database write is the whole
                // change - calling the bridge there would throw.
                if ($connected) {
                    $this->rcon->setMotto($user, $motto);
                }
            }

            if ($changes !== []) {
                $user->update($changes);
            }
        });

        $username = $data['username'] ?? null;

        if (is_string($username)) {
            $this->renameUser($user, $username);
        }
    }

    /** @throws ValidationException */
    private function renameUser(User $user, string $username): void
    {
        if ($user->username === $username) {
            return;
        }

        // The emulator grants a single rename; the driver decides where that
        // grant lives, and answers false outright when it has no schema for
        // one. Consume it again after a successful rename, as the game does.
        if (! $this->settings->canChangeName($user)) {
            throw ValidationException::withMessages([
                'username' => __('You are not allowed to change your username'),
            ]);
        }

        DB::transaction(function () use ($user, $username): void {
            $user->update(['username' => $username]);

            $this->settings->setCanChangeName($user, false);
        });
    }
}
