<?php

namespace App\Models\Community\Staff;

use App\Models\Community\Teams\WebsiteTeam;
use App\Models\Game\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $position_kind
 * @property int|null $permission_id
 * @property int|null $team_id
 * @property string $description
 * @property Carbon|null $apply_from
 * @property Carbon|null $apply_to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, WebsiteStaffApplications> $applications
 * @property-read int|null $applications_count
 * @property-read Permission|null $permission
 * @property-read WebsiteTeam|null $team
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition canApply()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition whereApplyFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition whereApplyTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition wherePositionKind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteOpenPosition whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteOpenPosition extends Model
{
    use HasFactory;

    protected $table = 'website_open_positions';

    protected $guarded = ['id'];

    protected $fillable = [
        'position_kind',
        'permission_id',
        'team_id',
        'description',
        'apply_from',
        'apply_to',
    ];

    protected $casts = [
        'apply_from' => 'datetime',
        'apply_to' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($openPosition) {
            if ($openPosition->position_kind === 'rank' && $openPosition->permission_id) {
                WebsiteStaffApplications::where('rank_id', $openPosition->permission_id)->delete();
            }
        });
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(WebsiteTeam::class, 'team_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(WebsiteStaffApplications::class, 'rank_id', 'permission_id');
    }

    public function scopeCanApply($query)
    {
        return $query->where('apply_from', '<=', now())->where('apply_to', '>', now());
    }
}
