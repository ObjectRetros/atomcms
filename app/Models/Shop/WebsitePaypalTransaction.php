<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $transaction_id
 * @property string|null $status
 * @property string|null $description
 * @property float $amount
 * @property string $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePaypalTransaction whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsitePaypalTransaction extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
