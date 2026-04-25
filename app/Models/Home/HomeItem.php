<?php

namespace App\Models\Home;

use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeItem extends Model
{
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

    public function homeCategory(): BelongsTo
    {
        return $this->belongsTo(HomeCategory::class);
    }

    public function userHomeItems(): HasMany
    {
        return $this->hasMany(UserHomeItem::class, 'home_item_id');
    }

    public function scopeEnabled(Builder $query): void
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
