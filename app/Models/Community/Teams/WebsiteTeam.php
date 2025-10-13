<?php

namespace App\Models\Community\Teams;

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

    public function openPositions(): HasMany
    {
        return $this->hasMany(\App\Models\Community\Staff\WebsiteOpenPosition::class, 'team_id', 'id');
    }
}
