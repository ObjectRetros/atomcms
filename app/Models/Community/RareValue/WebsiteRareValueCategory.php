<?php

namespace App\Models\Community\RareValue;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $badge
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, WebsiteRareValue> $furniture
 * @property-read int|null $furniture_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory whereBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValueCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteRareValueCategory extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function furniture(): HasMany
    {
        return $this->hasMany(WebsiteRareValue::class, 'category_id');
    }
}
