<?php

namespace App\Models\Community\Staff;

use App\Models\User;
use App\Models\Compositions\HasBadge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteTeam extends Model
{
    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'team_id', 'id');
    }

    public function getBadgePath(): string
    {
        return sprintf('%s%s.gif', setting('badges_path'), $this->getBadgeName());
    }

    public function getBadgeName(): string
    {
        return $this->badge ?: '';
    }
}
