<?php

namespace App\Models\Help;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $badge
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, WebsiteRule> $rules
 * @property-read int|null $rules_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory whereBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRuleCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteRuleCategory extends Model
{
    protected $guarded = [];

    public function rules(): HasMany
    {
        return $this->hasMany(WebsiteRule::class, 'category_id');
    }
}
