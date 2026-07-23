<?php

namespace App\Models\Home;

use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $home_category_id
 * @property HomeItemType $type
 * @property int $order
 * @property string $name
 * @property string $image
 * @property int $price
 * @property CurrencyTypes $currency_type
 * @property bool $enabled
 * @property int|null $limit
 * @property int $total_bought
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read HomeCategory|null $homeCategory
 * @property-read Collection<int, UserHomeItem> $userHomeItems
 * @property-read int|null $user_home_items_count
 */
class HomeItem extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $guarded = [];

    /** @var array<string, string> */
    protected array $availableWidgets = [
        'My Profile' => 'my-profile',
        'My Friends' => 'my-friends',
        'My Guestbook' => 'my-guestbook',
        'My Badges' => 'my-badges',
        'My Rooms' => 'my-rooms',
        'My Groups' => 'my-groups',
        'My Rating' => 'my-rating',
    ];

    protected function casts(): array
    {
        return [
            'type' => HomeItemType::class,
            'currency_type' => CurrencyTypes::class,
            'enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<HomeCategory, $this> */
    public function homeCategory(): BelongsTo
    {
        return $this->belongsTo(HomeCategory::class);
    }

    /** @return HasMany<UserHomeItem, $this> */
    public function userHomeItems(): HasMany
    {
        return $this->hasMany(UserHomeItem::class, 'home_item_id');
    }

    /** @param Builder<static> $query */
    #[Scope]
    protected function enabled(Builder $query): void
    {
        $query->where('enabled', true);
    }

    public function hasExceededPurchaseLimit(): bool
    {
        return $this->limit !== null && $this->total_bought >= $this->limit;
    }

    public function getDefaultTheme(): ?string
    {
        return match ($this->type) {
            HomeItemType::Note => 'note',
            HomeItemType::Widget => 'default',
            default => null,
        };
    }

    /**
     * @return array<string, string>
     */
    public function getAvailableWidgets(): array
    {
        return $this->availableWidgets;
    }
}
