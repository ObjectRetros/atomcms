<?php

namespace App\Models\Community\Staff;

use App\Models\Community\Teams\WebsiteTeam; // <-- adjust if your class lives elsewhere
use App\Models\Game\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteStaffApplications extends Model
{
    protected $table = 'website_staff_applications';

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'rank_id',
        'team_id',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'rank_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(WebsiteTeam::class, 'team_id');
    }
}
