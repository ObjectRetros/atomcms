<?php

namespace App\Models\Community\Teams;

use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Compositions\HasBadge;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $rank_name
 * @property int $hidden_rank
 * @property string|null $badge
 * @property string|null $job_description
 * @property string $staff_color
 * @property string $staff_background
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, WebsiteOpenPosition> $openPositions
 * @property-read int|null $open_positions_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereHiddenRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereJobDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereRankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereStaffBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereStaffColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteTeam whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteTeam extends Model implements HasBadge
{
    protected $table = 'website_teams';

    protected $fillable = [
        'rank_name',
        'hidden_rank',
        'badge',
        'job_description',
        'staff_color',
        'staff_background',
    ];

    /** @return HasMany<User, $this> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'team_id', 'id');
    }

    /** @return HasMany<WebsiteOpenPosition, $this> */
    public function openPositions(): HasMany
    {
        return $this->hasMany(WebsiteOpenPosition::class, 'team_id', 'id');
    }

    /**
     * @param  Builder<static>  $query
     *
     * @return Builder<static>
     */
    #[Scope]
    protected function visible(Builder $query): Builder
    {
        return $query->where('hidden_rank', false);
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
