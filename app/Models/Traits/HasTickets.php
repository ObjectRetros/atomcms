<?php

namespace App\Models\Traits;

use App\Models\Help\WebsiteHelpCenterTicket;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTickets
{
    public function tickets(): HasMany
    {
        return $this->hasMany(WebsiteHelpCenterTicket::class);
    }
}
