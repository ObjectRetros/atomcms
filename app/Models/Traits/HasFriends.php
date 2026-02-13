<?php

namespace App\Models\Traits;

use App\Models\Game\Player\MessengerFriendship;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasFriends
{
    public function friends(): HasMany
    {
        return $this->hasMany(MessengerFriendship::class, 'user_one_id');
    }

    public function getOnlineFriends(int $total = 10)
    {
        return $this->friends()
            ->select(['user_two_id', 'users.id', 'users.username', 'users.look', 'users.motto', 'users.last_online'])
            ->join('users', 'users.id', '=', 'user_two_id')
            ->where('users.online', '1')
            ->inRandomOrder()
            ->limit($total)
            ->get();
    }
}
