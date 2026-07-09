<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\BanRepository;
use App\Emulator\Data\BanInfo;
use App\Models\User;
use App\Models\User\Ban;

/**
 * Arcturus types each ban row: account/super target a user id, ip/machine
 * carry the offending address.
 */
class ArcturusBanRepository implements BanRepository
{
    public function activeIpBan(string $ip): ?BanInfo
    {
        return $this->toInfo(
            Ban::where('ip', $ip)
                ->where('ban_expire', '>', time())
                ->whereIn('type', ['ip', 'machine'])
                ->orderByDesc('id')
                ->first(),
        );
    }

    public function activeAccountBan(User $user): ?BanInfo
    {
        return $this->toInfo(
            Ban::where('user_id', $user->id)
                ->where('ban_expire', '>', time())
                ->whereIn('type', ['account', 'super'])
                ->orderByDesc('id')
                ->first(),
        );
    }

    private function toInfo(?Ban $ban): ?BanInfo
    {
        return $ban === null
            ? null
            : new BanInfo((string) $ban->ban_reason, (int) $ban->ban_expire);
    }
}
