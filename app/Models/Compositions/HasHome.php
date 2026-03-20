<?php

namespace App\Models\Compositions;

use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeItem;
use App\Models\Home\UserHomeMessage;
use App\Models\Home\UserHomeRating;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

trait HasHome
{
    public function homeItems(): HasMany
    {
        return $this->hasMany(UserHomeItem::class);
    }

    public function inventoryHomeItems(): HasMany
    {
        return $this->homeItems()
            ->defaultRelationships()
            ->where('placed', false);
    }

    public function groupedInventoryItems(): HasMany
    {
        return $this->inventoryHomeItems()
            ->select(DB::raw('home_item_id, JSON_ARRAYAGG(id) as item_ids'))
            ->groupBy('home_item_id');
    }

    public function placedHomeItems(): HasMany
    {
        return $this->homeItems()
            ->defaultRelationships()
            ->where('placed', true);
    }

    public function homeRatings(): HasMany
    {
        return $this->hasMany(UserHomeRating::class, 'rated_user_id');
    }

    public function receivedHomeMessages(): HasMany
    {
        return $this->hasMany(UserHomeMessage::class, 'recipient_user_id');
    }

    public function sentHomeMessages(): HasMany
    {
        return $this->hasMany(UserHomeMessage::class, 'user_id');
    }

    public function giveHomeItem(HomeItem $item, int $quantity = 1): void
    {
        $this->homeItems()->insert(
            array_fill(0, $quantity, [
                'user_id' => $this->id,
                'home_item_id' => $item->id,
                'theme' => $item->getDefaultTheme(),
                'created_at' => now(),
                'updated_at' => now(),
            ]),
        );

        $item->increment('total_bought', $quantity);
    }

    public function changeHomeBackground(UserHomeItem $background): void
    {
        $this->placedHomeItems()
            ->whereHas('homeItem', fn ($query) => $query->where('type', HomeItemType::Background))
            ->update(['placed' => false]);

        $background->update(['placed' => true]);
    }

    public function currencyAmount(CurrencyTypes $type): int
    {
        if ($type === CurrencyTypes::Credits) {
            return $this->credits;
        }

        if (! $this->relationLoaded('currencies')) {
            $this->load('currencies');
        }

        return $this->currencies->where('type', $type->value)->first()?->amount ?? 0;
    }

    public function discountCurrency(CurrencyTypes $type, int $amount): void
    {
        if ($type === CurrencyTypes::Credits) {
            $this->decrement('credits', $amount);

            return;
        }

        $this->currencies()->where('type', $type->value)->decrement('amount', $amount);
    }

    public function loadRoomsForHome(): self
    {
        return $this->load([
            'rooms' => fn (HasMany $query) => $query->select('id', 'owner_id', 'name', 'description', 'state'),
        ]);
    }

    public function loadRatingsForHome(): self
    {
        $this->homeRatingStats = $this->homeRatings()
            ->selectRaw('AVG(rating) as rating_avg, COUNT(*) as total, COUNT(IF(rating >= 4, 4, NULL)) as most_positive')
            ->first();

        $this->homeRatingStats->rating_avg = number_format($this->homeRatingStats->rating_avg ?? 0, 1);

        return $this;
    }

    public function loadFriendsForHome(string $routeName): self
    {
        $this->setRelation('friends',
            $this->friends()
                ->select('user_two_id')
                ->with('user:id,username,look,online')
                ->orderByDesc('id')
                ->paginate(8, ['*'], 'friends_page')
                ->withPath(route($routeName, $this->username)),
        );

        return $this;
    }

    public function loadBadgesForHome(string $routeName): self
    {
        $this->setRelation('badges',
            $this->badges()
                ->orderByDesc('id')
                ->paginate(16, ['*'], 'badges_page')
                ->withPath(route($routeName, $this->username)),
        );

        return $this;
    }

    public function loadGuestbookForHome(): self
    {
        return $this->load([
            'receivedHomeMessages' => fn (HasMany $query) => $query->latest()->defaultUserData(),
        ]);
    }
}
