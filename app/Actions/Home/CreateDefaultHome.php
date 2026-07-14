<?php

namespace App\Actions\Home;

use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\User;

class CreateDefaultHome
{
    public static function for(User $user): void
    {
        $ownedItemIds = $user->homeItems()->pluck('home_item_id');

        $hasBackground = HomeItem::whereIn('id', $ownedItemIds)
            ->where('type', HomeItemType::Background)
            ->exists();

        $hasProfileWidget = HomeItem::whereIn('id', $ownedItemIds)
            ->where('type', HomeItemType::Widget)
            ->where('name', 'My Profile')
            ->exists();

        $background = $hasBackground ? null : HomeItem::where('type', HomeItemType::Background)->orderBy('id')->first();
        $widget = $hasProfileWidget ? null : HomeItem::where('type', HomeItemType::Widget)->where('name', 'My Profile')->first();

        $items = array_values(array_filter([
            $background ? self::placedItem($user, $background, x: 0, y: 0, z: 0, theme: null) : null,
            $widget ? self::placedItem($user, $widget, x: 300, y: 100, z: 1, theme: 'default') : null,
        ]));

        if ($items) {
            $user->homeItems()->insert($items);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function placedItem(User $user, HomeItem $item, int $x, int $y, int $z, ?string $theme): array
    {
        $now = now();

        return [
            'user_id' => $user->id,
            'home_item_id' => $item->id,
            'x' => $x,
            'y' => $y,
            'z' => $z,
            'placed' => true,
            'theme' => $theme,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
