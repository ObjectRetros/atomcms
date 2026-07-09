<?php

namespace App\Emulator\Contracts;

use App\Emulator\Data\BanInfo;
use App\Models\User;

/**
 * Answers whether a visitor or account is banned on the emulator database.
 * Arcturus stores bans typed per row (account/ip/machine/super); Plus keys
 * them by bantype with the target in a value column.
 */
interface BanRepository
{
    public function activeIpBan(string $ip): ?BanInfo;

    public function activeAccountBan(User $user): ?BanInfo;
}
