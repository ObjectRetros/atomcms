<?php

namespace App\Models;

use App\Models\Compositions\HasBadge;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $category
 * @property int $level
 * @property int $reward_amount
 * @property int $reward_type
 * @property int|null $points
 * @property int $progress_needed
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereProgressNeeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereRewardAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Achievement whereRewardType($value)
 *
 * @mixin \Eloquent
 */
class Achievement extends Model implements HasBadge
{
    public $timestamps = false;

    protected $guarded = [];

    public function getBadgePath(): string
    {
        return sprintf('%sACH_%s.gif', setting('badges_path'), $this->getBadgeName());
    }

    public function getBadgeName(): string
    {
        return sprintf('%s%s', $this->name, (string) $this->level);
    }
}
