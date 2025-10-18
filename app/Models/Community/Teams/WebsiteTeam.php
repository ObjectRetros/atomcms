<?php

namespace App\Models\Community\Teams;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteTeam extends Model
{
    protected $table = 'website_teams';

    protected $guarded = ['id'];

    protected $fillable = [
        'rank_name',
        'hidden_rank',
        'badge',
        'job_description',
        'staff_color',
        'staff_background',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'team_id', 'id');
    }

    public function openPositions(): HasMany
    {
        return $this->hasMany(\App\Models\Community\Staff\WebsiteOpenPosition::class, 'team_id', 'id');
    }

    public function scopeVisible($query)
    {
        return $query->where('hidden_rank', false);
    }
}
