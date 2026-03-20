<?php

namespace Database\Seeders\Compositions\Home;

use Illuminate\Support\Facades\DB;

trait HasNotesCategoryData
{
    public function insertNotesItemsData()
    {
        $this->currentOrder = 1;

        DB::table('home_items')->insert([
            $this->buildItemStructure(null, 'https://imgur.com/SYdXCiz.png', 'Default Note', 15, 'n'),
        ]);
    }
}
