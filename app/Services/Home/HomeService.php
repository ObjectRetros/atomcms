<?php

namespace App\Services\Home;

use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\HomeItemType;
use App\Exceptions\HomePurchaseException;
use App\Models\Home\HomeCategory;
use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class HomeService
{
    public function __construct(private readonly CurrencyRepository $currencies) {}

    private function ensurePurchaseIsAllowed(User $user, HomeItem $item, int $quantity, int $totalPrice): void
    {
        if ($user->online) {
            throw new HomePurchaseException(__('You must be offline to buy this item.'));
        }

        if (! $item->enabled) {
            throw new HomePurchaseException(__('This item is not available for purchase.'));
        }

        if ($item->hasExceededPurchaseLimit()) {
            throw new HomePurchaseException(__('This item exceeded the purchase limit.'));
        }

        if ($item->limit !== null && ($item->total_bought + $quantity) > $item->limit) {
            throw new HomePurchaseException(__("You can't buy more than :max of this item.", [
                'max' => $item->limit - $item->total_bought,
            ]));
        }

        if ($totalPrice > $this->currencies->balance($user, $item->currency_type)) {
            throw new HomePurchaseException(__("You don't have enough :currency to buy this item.", [
                'currency' => strtolower(__($item->currency_type->name)),
            ]));
        }

        if (in_array($item->type, [HomeItemType::Background, HomeItemType::Widget])
            && $user->homeItems()->where('home_item_id', $item->id)->exists()) {
            throw new HomePurchaseException(__('You already have this item in your inventory.'));
        }

        if (in_array($item->type, [HomeItemType::Background, HomeItemType::Widget]) && $quantity > 1) {
            throw new HomePurchaseException(__('You can buy this item only once.'));
        }
    }

    public function buyItem(User $user, int $itemId, int $quantity): HomeItem
    {
        return DB::transaction(function () use ($user, $itemId, $quantity): HomeItem {
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $item = HomeItem::query()
                ->whereKey($itemId)
                ->lockForUpdate()
                ->firstOrFail();

            $totalPrice = $item->price * $quantity;

            $this->ensurePurchaseIsAllowed($lockedUser, $item, $quantity, $totalPrice);

            if (! $this->currencies->deduct($lockedUser, $item->currency_type, $totalPrice)) {
                throw new HomePurchaseException(__('Insufficient balance.'));
            }

            $lockedUser->giveHomeItem($item, $quantity);

            return $item;
        });
    }

    /** @param  array{backgroundId?: int, items?: list<array<string, mixed>>}  $data */
    public function saveItems(User $actor, User $user, array $data): void
    {
        abort_unless($actor->is($user), 403);
        if (isset($data['backgroundId'])) {
            $background = $user->inventoryHomeItems()->find($data['backgroundId']);

            if ($background) {
                $user->changeHomeBackground($background);
            }
        }

        if (! isset($data['items']) || count($data['items']) < 1) {
            return;
        }

        $itemsCollection = collect($data['items']);

        $allItems = $user->homeItems()
            ->defaultRelationships()
            ->whereIn('id', $itemsCollection->pluck('id'))
            ->get();

        DB::transaction(function () use ($itemsCollection, $allItems): void {
            $allItems->each(function (UserHomeItem $item) use ($itemsCollection): void {
                $itemData = $itemsCollection->where('id', $item->id)->first();
                $homeItem = $item->homeItem;

                if ($homeItem === null) {
                    return;
                }

                $item->placed = (bool) ($itemData['placed'] ?? $item->placed);
                $item->x = (int) ($itemData['x'] ?? $item->x);
                $item->y = (int) ($itemData['y'] ?? $item->y);
                $item->z = (int) ($itemData['z'] ?? $item->z);
                $item->is_reversed = (bool) ($itemData['is_reversed'] ?? $item->is_reversed);
                $item->theme = $itemData['theme'] ?? $homeItem->getDefaultTheme();

                if (! empty($itemData['extra_data'])) {
                    $item->extra_data = strip_tags($itemData['extra_data']);
                }

                if (! $item->placed && $homeItem->type === HomeItemType::Note) {
                    $item->extra_data = '';
                }

                if ($item->isDirty()) {
                    $item->save();
                }
            });
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLatestPurchaseItemIds(User $user, HomeItem $item, int $quantity): array
    {
        $results = DB::select(
            'SELECT hi.id, hi.type, hi.name, hi.image, uhi.home_item_id, JSON_ARRAYAGG(uhi.id) AS item_ids
            FROM (
                SELECT home_item_id, id
                FROM user_home_items
                WHERE user_id = ?
                AND placed = ?
                AND home_item_id = ?
                ORDER BY id DESC
                LIMIT ?
            ) AS uhi
            JOIN home_items hi ON hi.id = uhi.home_item_id
            GROUP BY hi.id, hi.type, hi.name, hi.image, uhi.home_item_id',
            [$user->id, 0, $item->id, $quantity],
        );

        return array_map(fn ($row) => [
            'home_item_id' => $row->home_item_id,
            'item_ids' => json_decode($row->item_ids),
            'home_item' => [
                'id' => $row->id,
                'type' => $row->type,
                'name' => $row->name,
                'image' => $row->image,
            ],
        ], $results);
    }

    /** @return Collection<int, HomeCategory> */
    public function categories(): Collection
    {
        return HomeCategory::orderBy('order')->get();
    }

    /** @return Collection<int, HomeItem> */
    public function catalogItems(?HomeCategory $category = null, ?HomeItemType $type = null): Collection
    {
        return HomeItem::enabled()->when($category !== null, fn ($query) => $query->where('home_category_id', $category?->id))->when($type !== null, fn ($query) => $query->where('type', $type))->orderBy('order')->get();
    }

    /** @return Collection<int, UserHomeItem> */
    public function inventory(User $actor, User $owner, bool $grouped = false): Collection
    {
        abort_unless($actor->is($owner), 403);
        $query = $grouped ? $owner->groupedInventoryItems() : $owner->inventoryHomeItems()->with('homeItem');

        return $query->get();
    }

    /** @return Collection<int, UserHomeItem> */
    public function placedItems(User $user): Collection
    {
        return $user->placedHomeItems()->defaultRelationships(true)->get();
    }

    public function postMessage(User $actor, User $owner, string $content): void
    {
        abort_if($actor->is($owner), 403);
        if ($actor->sentHomeMessages()->where('created_at', '>', now()->subMinute())->exists()) {
            throw new TooManyRequestsHttpException(60, __('You are sending messages too fast.'));
        }
        $owner->receivedHomeMessages()->create(['user_id' => $actor->id, 'content' => strip_tags($content)]);
    }

    public function rate(User $actor, User $owner, int $rating): void
    {
        abort_if($actor->is($owner), 403);
        $owner->homeRatings()->updateOrCreate(['user_id' => $actor->id], ['rating' => $rating]);
    }

    public function getWidgetContent(User $user, UserHomeItem $item): ?string
    {
        $viewName = "home.widgets.{$item->widget_type}";

        if (! view()->exists($viewName)) {
            return null;
        }

        $user = $this->loadWidgetData($user, $item);

        return view($viewName, compact('item', 'user'))->render();
    }

    public function loadWidgetData(User $user, UserHomeItem $item, string $routeName = 'home.show'): User
    {
        return match ($item->widget_type) {
            'my-rooms' => $user->loadRoomsForHome(),
            'my-badges' => $user->loadBadgesForHome($routeName),
            'my-friends' => $user->loadFriendsForHome($routeName),
            'my-rating' => $user->loadRatingsForHome(),
            'my-guestbook' => $user->loadGuestbookForHome(),
            default => $user,
        };
    }
}
