<?php

namespace Database\Seeders\Compositions\Home;

use Illuminate\Support\Facades\DB;

trait HasWidgetsCategoryData
{
    public function insertWidgetsItemsData()
    {
        $this->currentOrder = 1;

        DB::table('home_items')->insert([
            $this->buildItemStructure(null, 'https://imgur.com/MZiw18o.png', 'My Profile', 30, 'w'),
            $this->buildItemStructure(null, 'https://imgur.com/Ac7XcJQ.png', 'My Friends', 30, 'w'),
            $this->buildItemStructure(null, 'https://imgur.com/iNcpO2q.png', 'My Guestbook', 30, 'w'),
            $this->buildItemStructure(null, 'https://imgur.com/gWpDn8t.png', 'My Badges', 30, 'w'),
            $this->buildItemStructure(null, 'https://imgur.com/9h35Bkm.png', 'My Rooms', 30, 'w'),
            $this->buildItemStructure(null, 'https://imgur.com/oNDGmYS.png', 'My Groups', 30, 'w'),
            $this->buildItemStructure(null, 'https://imgur.com/2dkPaE9.png', 'My Rating', 30, 'w'),
        ]);
    }
}
