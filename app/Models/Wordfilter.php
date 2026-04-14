<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $key The word to filter.
 * @property string $replacement What the word should be replaced with.
 * @property string $hide Wether the whole message that contains this word should be hidden from being displayed.
 * @property string $report Wether the message should be reported as auto-report to the moderators.
 * @property int $mute Time user gets muted for mentioning this word.
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter whereMute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter whereReplacement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wordfilter whereReport($value)
 *
 * @mixin \Eloquent
 */
class Wordfilter extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $guarded = [];

    protected $table = 'wordfilter';

    protected $primaryKey = 'key';

    public $timestamps = false;

    public $incrementing = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
