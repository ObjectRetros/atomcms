<?php

namespace App\Models\Community\Staff;

use App\Models\Game\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteStaffApplications extends Model
{
    protected $guarded = ['id'];

    protected $table = 'website_staff_applications';

    protected $fillable = [
        'user_id',
        'rank_id',
        'content',
    ];

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'rank_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
