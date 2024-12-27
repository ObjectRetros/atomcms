<?php

namespace App\Models\Game;

use App\Models\User;
use App\Models\Compositions\HasBadge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model implements HasBadge
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'rank', 'id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(PermissionRole::class);
    }

    public function getBadgePath(): string
    {
        return sprintf('%s%s.gif', setting('badges_path'), $this->getBadgeName());
    }

    public function getBadgeName(): string
    {
        return $this->badge;
    }
}
