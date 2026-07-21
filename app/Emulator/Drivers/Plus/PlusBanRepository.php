<?php

namespace App\Emulator\Drivers\Plus;

use App\Emulator\Contracts\BanRepository;
use App\Emulator\Data\BanInfo;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Plus EMU keys bans by bantype with the target in the value column: 'ip'
 * bans store the address, 'user' bans store the username.
 */
class PlusBanRepository implements BanRepository
{
    public function activeIpBan(string $ip): ?BanInfo
    {
        return $this->toInfo(
            $this->activeBans()
                ->where('bantype', 'ip')
                ->where('value', $ip)
                ->orderByDesc('id')
                ->first(),
        );
    }

    public function activeAccountBan(User $user): ?BanInfo
    {
        return $this->toInfo(
            $this->activeBans()
                ->where('bantype', 'user')
                ->where('value', $user->username)
                ->orderByDesc('id')
                ->first(),
        );
    }

    private function activeBans(): Builder
    {
        return DB::table('bans')->where('expire', '>', time());
    }

    private function toInfo(?object $ban): ?BanInfo
    {
        return $ban === null
            ? null
            : new BanInfo(
                (string) data_get($ban, 'reason'),
                (int) data_get($ban, 'expire'),
            );
    }
}
