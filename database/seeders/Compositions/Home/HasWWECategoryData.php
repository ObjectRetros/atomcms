<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasWWECategoryData
{
    public function getWWEItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, '/assets/images/home/items/B5zuCA8.png', 'WWE: Balls Mahoney'),
            $this->buildItemStructure($category, '/assets/images/home/items/WBOBopI.png', 'WWE: Batista'),
            $this->buildItemStructure($category, '/assets/images/home/items/L1G0hIl.png', 'WWE: Beth Phoenix'),
            $this->buildItemStructure($category, '/assets/images/home/items/WVJxsEH.png', 'WWE: Superstar Billy Graham'),
            $this->buildItemStructure($category, '/assets/images/home/items/y1xotK7.png', 'WWE: Cowboy Bob Orton'),
            $this->buildItemStructure($category, '/assets/images/home/items/VAKXalR.png', 'WWE: Boogeyman'),
            $this->buildItemStructure($category, '/assets/images/home/items/KSLGDxD.png', 'WWE: Carlito'),
            $this->buildItemStructure($category, '/assets/images/home/items/1wAaBKG.png', 'WWE: John Cena'),
            $this->buildItemStructure($category, '/assets/images/home/items/M0RQc9h.png', 'WWE: Chuck Palumbo'),
            $this->buildItemStructure($category, '/assets/images/home/items/kf8itR8.png', 'WWE: CM Punk'),
            $this->buildItemStructure($category, '/assets/images/home/items/4cikNki.png', 'WWE: Curt Hawkins'),
            $this->buildItemStructure($category, '/assets/images/home/items/N5Gi1SS.png', 'WWE: DH Smith'),
            $this->buildItemStructure($category, '/assets/images/home/items/MD8okz0.png', 'WWE: EDGE'),
            $this->buildItemStructure($category, '/assets/images/home/items/TpiqWiy.png', 'WWE: Elijah Burke'),
            $this->buildItemStructure($category, '/assets/images/home/items/3kFuBP4.png', 'WWE: Festus'),
            $this->buildItemStructure($category, '/assets/images/home/items/jBvQsyC.png', 'WWE: Funaki'),
            $this->buildItemStructure($category, '/assets/images/home/items/jQnLNhj.png', 'WWE: Hackshaw Jim Duggan'),
            $this->buildItemStructure($category, '/assets/images/home/items/ALEocMU.png', 'WWE: Hornswoggle'),
            $this->buildItemStructure($category, '/assets/images/home/items/oTDGtk4.png', 'WWE: James Curtis'),
            $this->buildItemStructure($category, '/assets/images/home/items/OMJvmgo.png', 'WWE: Jeff Hardy'),
            $this->buildItemStructure($category, '/assets/images/home/items/wIMaE89.png', 'WWE: Jesse'),
            $this->buildItemStructure($category, '/assets/images/home/items/UFhOTZg.png', 'WWE: Mounth of South Jimmy Hart'),
            $this->buildItemStructure($category, '/assets/images/home/items/0dQST2q.png', 'WWE: Jimmy Superfly Snuka'),
            $this->buildItemStructure($category, '/assets/images/home/items/rbM0XVF.png', 'WWE: John Morrison'),
            $this->buildItemStructure($category, '/assets/images/home/items/P5SCRjL.png', 'WWE: Kenny Dykstra'),
            $this->buildItemStructure($category, '/assets/images/home/items/THiPUzK.png', 'WWE: Kevin Thorn'),
            $this->buildItemStructure($category, '/assets/images/home/items/i0UpliW.png', 'WWE: Bobby Lashley'),
            $this->buildItemStructure($category, '/assets/images/home/items/aKUGgRz.png', 'WWE: Mark Henry'),
            $this->buildItemStructure($category, '/assets/images/home/items/zVMCbpt.png', 'WWE: Matt Striker'),
            $this->buildItemStructure($category, '/assets/images/home/items/7jpkbLM.png', 'WWE: Mike Knoxx'),
            $this->buildItemStructure($category, '/assets/images/home/items/j6EpE0i.png', 'WWE: Mr. Kennedy'),
            $this->buildItemStructure($category, '/assets/images/home/items/lh4EEkR.png', 'WWE: MVP'),
            $this->buildItemStructure($category, '/assets/images/home/items/2iFcZgM.png', 'WWE: Nunzio'),
            $this->buildItemStructure($category, '/assets/images/home/items/6YwSWRF.png', 'WWE: Paul London'),
            $this->buildItemStructure($category, '/assets/images/home/items/r6DVbI2.png', 'WWE: Mr. Wonderful Paul Orndorff'),
            $this->buildItemStructure($category, '/assets/images/home/items/Xss3lkm.png', 'WWE: Randy Orton'),
            $this->buildItemStructure($category, '/assets/images/home/items/5WGKd1m.png', 'WWE: Rey Mysterio'),
            $this->buildItemStructure($category, '/assets/images/home/items/lzN9xCw.png', 'WWE: Ric Flair'),
            $this->buildItemStructure($category, '/assets/images/home/items/RIGkvrV.png', 'WWE: Rowdy Roddy Piper'),
            $this->buildItemStructure($category, '/assets/images/home/items/5wFAvFu.png', 'WWE: Ron Simmons'),
            $this->buildItemStructure($category, '/assets/images/home/items/JdvpiLK.png', 'WWE: Santino Marella'),
            $this->buildItemStructure($category, '/assets/images/home/items/nK8oUEI.png', 'WWE: Sgt. Slaughter'),
            $this->buildItemStructure($category, '/assets/images/home/items/NyAnrtt.png', 'WWE: Snitsky'),
            $this->buildItemStructure($category, '/assets/images/home/items/DMaAsUi.png', 'WWE: Stevie Richards'),
            $this->buildItemStructure($category, '/assets/images/home/items/9pKriRF.png', 'WWE: Stone Cold Steve Austin'),
            $this->buildItemStructure($category, '/assets/images/home/items/GESNpcH.png', 'WWE: Super Crazy'),
            $this->buildItemStructure($category, '/assets/images/home/items/xRFvi2s.png', 'WWE: The Great Khali'),
            $this->buildItemStructure($category, '/assets/images/home/items/FT6qlff.png', 'WWE: The Miz'),
            $this->buildItemStructure($category, '/assets/images/home/items/swGr2dV.png', 'WWE: Tommy Dreamer'),
            $this->buildItemStructure($category, '/assets/images/home/items/u8yMZXg.png', 'WWE: Torrie Wilson'),
            $this->buildItemStructure($category, '/assets/images/home/items/knVrdvM.png', 'WWE: Triple H'),
            $this->buildItemStructure($category, '/assets/images/home/items/JSeaLux.png', 'WWE: Umaga'),
            $this->buildItemStructure($category, '/assets/images/home/items/X1fJfqY.png', 'WWE: Undertaker'),
            $this->buildItemStructure($category, '/assets/images/home/items/Cs0tm6Y.png', 'WWE: Big Daddy V'),
            $this->buildItemStructure($category, '/assets/images/home/items/8jysKTD.png', 'WWE: Shawn Michaels'),
            $this->buildItemStructure($category, '/assets/images/home/items/arJWRvr.png', 'WWE: Val Venis'),
            $this->buildItemStructure($category, '/assets/images/home/items/9Ny7Vv8.png', 'WWE: Victoria'),
            $this->buildItemStructure($category, '/assets/images/home/items/lEJnIfO.png', 'WWE: Zack Ryder'),
        ];
    }
}
