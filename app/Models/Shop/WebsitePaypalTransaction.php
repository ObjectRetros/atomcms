<?php

namespace App\Models\Shop;

use App\Enums\PaypalTransactionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $transaction_id
 * @property PaypalTransactionStatus|null $status
 * @property string|null $description
 * @property int $amount Amount in the currency's minor unit
 * @property string $currency
 * @property string|null $capture_id
 * @property Carbon|null $credited_at
 * @property Carbon|null $last_reconciled_at
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
    public const PaypalTransactionStatus STATUS_CANCELLED = PaypalTransactionStatus::Cancelled;

    public const PaypalTransactionStatus STATUS_COMPLETED = PaypalTransactionStatus::Completed;

    public const PaypalTransactionStatus STATUS_CREATED = PaypalTransactionStatus::Created;

    public const PaypalTransactionStatus STATUS_LEGACY_CREATED = PaypalTransactionStatus::LegacyCreated;

    public const PaypalTransactionStatus STATUS_REVIEW = PaypalTransactionStatus::Review;

    protected $fillable = [
        'transaction_id',
        'capture_id',
        'status',
        'description',
        'amount',
        'currency',
        'credited_at',
        'last_reconciled_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => PaypalTransactionStatus::class,
            'credited_at' => 'datetime',
            'last_reconciled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
