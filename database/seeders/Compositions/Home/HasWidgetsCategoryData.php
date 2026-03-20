<?php

namespace Database\Seeders\Compositions\Home;

use Illuminate\Support\Facades\DB;

trait HasWidgetsCategoryData
{
    public function insertWidgetsItemsData()
    {
        $this->currentOrder = 1;

        DB::table('home_items')->insert([
            $this->buildItemStructure(null, '/assets/images/home/items/MZiw18o.png', 'My Profile', 30, 'w'),
            $this->buildItemStructure(null, '/assets/images/home/items/Ac7XcJQ.png', 'My Friends', 30, 'w'),
            $this->buildItemStructure(null, '/assets/images/home/items/iNcpO2q.png', 'My Guestbook', 30, 'w'),
            $this->buildItemStructure(null, '/assets/images/home/items/gWpDn8t.png', 'My Badges', 30, 'w'),
            $this->buildItemStructure(null, '/assets/images/home/items/9h35Bkm.png', 'My Rooms', 30, 'w'),
            $this->buildItemStructure(null, '/assets/images/home/items/oNDGmYS.png', 'My Groups', 30, 'w'),
            $this->buildItemStructure(null, '/assets/images/home/items/2dkPaE9.png', 'My Rating', 30, 'w'),
        ]);
    }
}
