<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasWWECategoryData
{
    public function getWWEItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'https://imgur.com/B5zuCA8.png', 'WWE: Balls Mahoney'),
            $this->buildItemStructure($category, 'https://imgur.com/WBOBopI.png', 'WWE: Batista'),
            $this->buildItemStructure($category, 'https://imgur.com/L1G0hIl.png', 'WWE: Beth Phoenix'),
            $this->buildItemStructure($category, 'https://imgur.com/WVJxsEH.png', 'WWE: Superstar Billy Graham'),
            $this->buildItemStructure($category, 'https://imgur.com/y1xotK7.png', 'WWE: Cowboy Bob Orton'),
            $this->buildItemStructure($category, 'https://imgur.com/VAKXalR.png', 'WWE: Boogeyman'),
            $this->buildItemStructure($category, 'https://imgur.com/KSLGDxD.png', 'WWE: Carlito'),
            $this->buildItemStructure($category, 'https://imgur.com/1wAaBKG.png', 'WWE: John Cena'),
            $this->buildItemStructure($category, 'https://imgur.com/M0RQc9h.png', 'WWE: Chuck Palumbo'),
            $this->buildItemStructure($category, 'https://imgur.com/kf8itR8.png', 'WWE: CM Punk'),
            $this->buildItemStructure($category, 'https://imgur.com/4cikNki.png', 'WWE: Curt Hawkins'),
            $this->buildItemStructure($category, 'https://imgur.com/N5Gi1SS.png', 'WWE: DH Smith'),
            $this->buildItemStructure($category, 'https://imgur.com/MD8okz0.png', 'WWE: EDGE'),
            $this->buildItemStructure($category, 'https://imgur.com/TpiqWiy.png', 'WWE: Elijah Burke'),
            $this->buildItemStructure($category, 'https://imgur.com/3kFuBP4.png', 'WWE: Festus'),
            $this->buildItemStructure($category, 'https://imgur.com/jBvQsyC.png', 'WWE: Funaki'),
            $this->buildItemStructure($category, 'https://imgur.com/jQnLNhj.png', 'WWE: Hackshaw Jim Duggan'),
            $this->buildItemStructure($category, 'https://imgur.com/ALEocMU.png', 'WWE: Hornswoggle'),
            $this->buildItemStructure($category, 'https://imgur.com/oTDGtk4.png', 'WWE: James Curtis'),
            $this->buildItemStructure($category, 'https://imgur.com/OMJvmgo.png', 'WWE: Jeff Hardy'),
            $this->buildItemStructure($category, 'https://imgur.com/wIMaE89.png', 'WWE: Jesse'),
            $this->buildItemStructure($category, 'https://imgur.com/UFhOTZg.png', 'WWE: Mounth of South Jimmy Hart'),
            $this->buildItemStructure($category, 'https://imgur.com/0dQST2q.png', 'WWE: Jimmy Superfly Snuka'),
            $this->buildItemStructure($category, 'https://imgur.com/rbM0XVF.png', 'WWE: John Morrison'),
            $this->buildItemStructure($category, 'https://imgur.com/P5SCRjL.png', 'WWE: Kenny Dykstra'),
            $this->buildItemStructure($category, 'https://imgur.com/THiPUzK.png', 'WWE: Kevin Thorn'),
            $this->buildItemStructure($category, 'https://imgur.com/i0UpliW.png', 'WWE: Bobby Lashley'),
            $this->buildItemStructure($category, 'https://imgur.com/aKUGgRz.png', 'WWE: Mark Henry'),
            $this->buildItemStructure($category, 'https://imgur.com/zVMCbpt.png', 'WWE: Matt Striker'),
            $this->buildItemStructure($category, 'https://imgur.com/7jpkbLM.png', 'WWE: Mike Knoxx'),
            $this->buildItemStructure($category, 'https://imgur.com/j6EpE0i.png', 'WWE: Mr. Kennedy'),
            $this->buildItemStructure($category, 'https://imgur.com/lh4EEkR.png', 'WWE: MVP'),
            $this->buildItemStructure($category, 'https://imgur.com/2iFcZgM.png', 'WWE: Nunzio'),
            $this->buildItemStructure($category, 'https://imgur.com/6YwSWRF.png', 'WWE: Paul London'),
            $this->buildItemStructure($category, 'https://imgur.com/r6DVbI2.png', 'WWE: Mr. Wonderful Paul Orndorff'),
            $this->buildItemStructure($category, 'https://imgur.com/Xss3lkm.png', 'WWE: Randy Orton'),
            $this->buildItemStructure($category, 'https://imgur.com/5WGKd1m.png', 'WWE: Rey Mysterio'),
            $this->buildItemStructure($category, 'https://imgur.com/lzN9xCw.png', 'WWE: Ric Flair'),
            $this->buildItemStructure($category, 'https://imgur.com/RIGkvrV.png', 'WWE: Rowdy Roddy Piper'),
            $this->buildItemStructure($category, 'https://imgur.com/5wFAvFu.png', 'WWE: Ron Simmons'),
            $this->buildItemStructure($category, 'https://imgur.com/JdvpiLK.png', 'WWE: Santino Marella'),
            $this->buildItemStructure($category, 'https://imgur.com/nK8oUEI.png', 'WWE: Sgt. Slaughter'),
            $this->buildItemStructure($category, 'https://imgur.com/NyAnrtt.png', 'WWE: Snitsky'),
            $this->buildItemStructure($category, 'https://imgur.com/DMaAsUi.png', 'WWE: Stevie Richards'),
            $this->buildItemStructure($category, 'https://imgur.com/9pKriRF.png', 'WWE: Stone Cold Steve Austin'),
            $this->buildItemStructure($category, 'https://imgur.com/GESNpcH.png', 'WWE: Super Crazy'),
            $this->buildItemStructure($category, 'https://imgur.com/xRFvi2s.png', 'WWE: The Great Khali'),
            $this->buildItemStructure($category, 'https://imgur.com/FT6qlff.png', 'WWE: The Miz'),
            $this->buildItemStructure($category, 'https://imgur.com/swGr2dV.png', 'WWE: Tommy Dreamer'),
            $this->buildItemStructure($category, 'https://imgur.com/u8yMZXg.png', 'WWE: Torrie Wilson'),
            $this->buildItemStructure($category, 'https://imgur.com/knVrdvM.png', 'WWE: Triple H'),
            $this->buildItemStructure($category, 'https://imgur.com/JSeaLux.png', 'WWE: Umaga'),
            $this->buildItemStructure($category, 'https://imgur.com/X1fJfqY.png', 'WWE: Undertaker'),
            $this->buildItemStructure($category, 'https://imgur.com/Cs0tm6Y.png', 'WWE: Big Daddy V'),
            $this->buildItemStructure($category, 'https://imgur.com/8jysKTD.png', 'WWE: Shawn Michaels'),
            $this->buildItemStructure($category, 'https://imgur.com/arJWRvr.png', 'WWE: Val Venis'),
            $this->buildItemStructure($category, 'https://imgur.com/9Ny7Vv8.png', 'WWE: Victoria'),
            $this->buildItemStructure($category, 'https://imgur.com/lEJnIfO.png', 'WWE: Zack Ryder'),
        ];
    }
}
