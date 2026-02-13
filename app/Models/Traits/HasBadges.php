<?php

namespace App\Models\Traits;

use App\Models\Game\Player\UserBadge;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasBadges
{
    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }
}
