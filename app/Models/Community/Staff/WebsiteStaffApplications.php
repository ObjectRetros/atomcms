<?php

namespace App\Models\Community\Staff;

use App\Models\Community\Teams\WebsiteTeam;
use App\Models\Game\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $rank_id
 * @property int|null $team_id
 * @property string $content
 * @property string $status
 * @property int|null $approved_by
 * @property Carbon|null $approved_at
 * @property int|null $rejected_by
 * @property Carbon|null $rejected_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $approver
 * @property-read Permission|null $rank
 * @property-read User|null $rejector
 * @property-read WebsiteTeam|null $team
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereRejectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteStaffApplications whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteStaffApplications extends Model
{
    protected $table = 'website_staff_applications';

    protected $fillable = [
        'user_id',
        'rank_id',
        'team_id',
        'content',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

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
