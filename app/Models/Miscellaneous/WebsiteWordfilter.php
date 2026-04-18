<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $word
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteWordfilter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteWordfilter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteWordfilter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteWordfilter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteWordfilter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteWordfilter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteWordfilter whereWord($value)
 *
 * @mixin \Eloquent
 */
class WebsiteWordfilter extends Model
{
    protected $table = 'website_wordfilter';

    protected $guarded = ['id'];
}
