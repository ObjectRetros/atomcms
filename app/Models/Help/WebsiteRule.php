<?php

namespace App\Models\Help;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $paragraph
 * @property string $rule
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WebsiteRuleCategory|null $category
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereParagraph($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteRule extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteRuleCategory::class, 'category_id');
    }
}
