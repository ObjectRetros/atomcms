<?php

namespace Database\Seeders\Compositions\Home;

use Illuminate\Support\Facades\DB;

trait HasWidgetsCategoryData
{
    public function insertWidgetsItemsData()
    {
        $this->currentOrder = 1;

        DB::table('home_items')->insert([
            $this->buildItemStructure(null, 'home-items/widget-my-profile.png', 'My Profile', 30, 'w'),
            $this->buildItemStructure(null, 'home-items/widget-my-friends.png', 'My Friends', 30, 'w'),
            $this->buildItemStructure(null, 'home-items/widget-my-guestbook.png', 'My Guestbook', 30, 'w'),
            $this->buildItemStructure(null, 'home-items/widget-my-badges.png', 'My Badges', 30, 'w'),
            $this->buildItemStructure(null, 'home-items/widget-my-rooms.png', 'My Rooms', 30, 'w'),
            $this->buildItemStructure(null, 'home-items/widget-my-groups.png', 'My Groups', 30, 'w'),
            $this->buildItemStructure(null, 'home-items/widget-my-rating.png', 'My Rating', 30, 'w'),
        ]);
    }
}
