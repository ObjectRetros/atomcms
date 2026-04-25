<?php

namespace App\Services\Home;

use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeItem;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeService
{
    /**
     * @throws \Exception
     */
    private function ensurePurchaseIsAllowed(User $user, HomeItem $item, int $quantity, int $totalPrice): void
    {
        if ($user->online) {
            throw new \Exception(__('You must be offline to buy this item.'));
        }

        if (! $item->enabled) {
            throw new \Exception(__('This item is not available for purchase.'));
        }

        if ($item->hasExceededPurchaseLimit()) {
            throw new \Exception(__('This item exceeded the purchase limit.'));
        }

        if ($item->limit !== null && ($item->total_bought + $quantity) > $item->limit) {
            throw new \Exception(__("You can't buy more than :max of this item.", [
                'max' => $item->limit - $item->total_bought,
            ]));
        }

        if ($totalPrice > $user->currencyAmount($item->currency_type)) {
            throw new \Exception(__("You don't have enough :currency to buy this item.", [
                'currency' => strtolower(__($item->currency_type->name)),
            ]));
        }

        if (in_array($item->type, [HomeItemType::Background, HomeItemType::Widget])
            && $user->homeItems()->where('home_item_id', $item->id)->exists()) {
            throw new \Exception(__('You already have this item in your inventory.'));
        }

        if (in_array($item->type, [HomeItemType::Background, HomeItemType::Widget]) && $quantity > 1) {
            throw new \Exception(__('You can buy this item only once.'));
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

            $lockedUser->discountCurrency($item->currency_type, $totalPrice);
            $lockedUser->giveHomeItem($item, $quantity);

            return $item;
        });
    }

    public function saveItems(User $user, array $data): void
    {
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

                $item->placed = (bool) ($itemData['placed'] ?? $item->placed);
                $item->x = (int) ($itemData['x'] ?? $item->x);
                $item->y = (int) ($itemData['y'] ?? $item->y);
                $item->z = (int) ($itemData['z'] ?? $item->z);
                $item->is_reversed = (bool) ($itemData['is_reversed'] ?? $item->is_reversed);
                $item->theme = $itemData['theme'] ?? $item->homeItem->getDefaultTheme();

                if (! empty($itemData['extra_data'])) {
                    $item->extra_data = strip_tags($itemData['extra_data']);
                }

                if (! $item->placed && $item->homeItem->type === HomeItemType::Note) {
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

    public function getWidgetContent(User $user, UserHomeItem $item): ?string
    {
        $viewName = "home.widgets.{$item->widget_type}";

        if (! view()->exists($viewName)) {
            return null;
        }

        $cacheKey = "user_{$user->id}_widget_{$item->id}_html";
        $cacheDuration = in_array($item->widget_type, ['my-rating', 'my-guestbook']) ? 0 : 300;

        $render = function () use ($user, $item, $viewName): string {
            $user = $this->loadWidgetData($user, $item);

            return view($viewName, compact('item', 'user'))->render();
        };

        return $cacheDuration > 0
            ? Cache::remember($cacheKey, $cacheDuration, $render)
            : $render();
    }

    public function clearWidgetCache(User $user, UserHomeItem $widget): void
    {
        Cache::forget("user_{$user->id}_widget_{$widget->id}_html");
    }

    private function loadWidgetData(User $user, UserHomeItem $item): User
    {
        return match ($item->widget_type) {
            'my-rooms' => $user->loadRoomsForHome(),
            'my-badges' => $user->loadBadgesForHome('home.show'),
            'my-friends' => $user->loadFriendsForHome('home.show'),
            'my-rating' => $user->loadRatingsForHome(),
            'my-guestbook' => $user->loadGuestbookForHome(),
            default => $user,
        };
    }
}
