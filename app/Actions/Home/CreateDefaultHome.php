<?php

namespace App\Actions\Home;

use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\User;

class CreateDefaultHome
{
    public static function for(User $user): void
    {
        if ($user->homeItems()->exists()) {
            return;
        }

        $background = HomeItem::where('type', HomeItemType::Background)->orderBy('id')->first();
        $widget = HomeItem::where('type', HomeItemType::Widget)->where('name', 'My Profile')->first();

        $items = [];
        $now = now();

        if ($background) {
            $items[] = [
                'user_id' => $user->id,
                'home_item_id' => $background->id,
                'x' => 0,
                'y' => 0,
                'z' => 0,
                'placed' => true,
                'theme' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($widget) {
            $items[] = [
                'user_id' => $user->id,
                'home_item_id' => $widget->id,
                'x' => 300,
                'y' => 100,
                'z' => 1,
                'placed' => true,
                'theme' => 'default',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($items) {
            $user->homeItems()->insert($items);
        }
    }
}
