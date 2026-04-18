<?php

namespace Database\Seeders\Compositions\Home;

use App\Models\Home\HomeCategory;

trait HasWWECategoryData
{
    public function getWWEItemsData(HomeCategory $category): array
    {
        return [
            $this->buildItemStructure($category, 'home-items/wwe-wwe-balls-mahoney.png', 'WWE: Balls Mahoney'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-batista.png', 'WWE: Batista'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-beth-phoenix.png', 'WWE: Beth Phoenix'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-superstar-billy-graham.png', 'WWE: Superstar Billy Graham'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-cowboy-bob-orton.png', 'WWE: Cowboy Bob Orton'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-boogeyman.png', 'WWE: Boogeyman'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-carlito.png', 'WWE: Carlito'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-john-cena.png', 'WWE: John Cena'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-chuck-palumbo.png', 'WWE: Chuck Palumbo'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-cm-punk.png', 'WWE: CM Punk'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-curt-hawkins.png', 'WWE: Curt Hawkins'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-dh-smith.png', 'WWE: DH Smith'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-edge.png', 'WWE: EDGE'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-elijah-burke.png', 'WWE: Elijah Burke'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-festus.png', 'WWE: Festus'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-funaki.png', 'WWE: Funaki'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-hackshaw-jim-duggan.png', 'WWE: Hackshaw Jim Duggan'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-hornswoggle.png', 'WWE: Hornswoggle'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-james-curtis.png', 'WWE: James Curtis'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-jeff-hardy.png', 'WWE: Jeff Hardy'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-jesse.png', 'WWE: Jesse'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-mounth-of-south-jimmy-hart.png', 'WWE: Mounth of South Jimmy Hart'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-jimmy-superfly-snuka.png', 'WWE: Jimmy Superfly Snuka'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-john-morrison.png', 'WWE: John Morrison'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-kenny-dykstra.png', 'WWE: Kenny Dykstra'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-kevin-thorn.png', 'WWE: Kevin Thorn'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-bobby-lashley.png', 'WWE: Bobby Lashley'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-mark-henry.png', 'WWE: Mark Henry'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-matt-striker.png', 'WWE: Matt Striker'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-mike-knoxx.png', 'WWE: Mike Knoxx'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-mr-kennedy.png', 'WWE: Mr. Kennedy'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-mvp.png', 'WWE: MVP'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-nunzio.png', 'WWE: Nunzio'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-paul-london.png', 'WWE: Paul London'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-mr-wonderful-paul-orndorff.png', 'WWE: Mr. Wonderful Paul Orndorff'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-randy-orton.png', 'WWE: Randy Orton'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-rey-mysterio.png', 'WWE: Rey Mysterio'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-ric-flair.png', 'WWE: Ric Flair'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-rowdy-roddy-piper.png', 'WWE: Rowdy Roddy Piper'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-ron-simmons.png', 'WWE: Ron Simmons'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-santino-marella.png', 'WWE: Santino Marella'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-sgt-slaughter.png', 'WWE: Sgt. Slaughter'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-snitsky.png', 'WWE: Snitsky'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-stevie-richards.png', 'WWE: Stevie Richards'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-stone-cold-steve-austin.png', 'WWE: Stone Cold Steve Austin'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-super-crazy.png', 'WWE: Super Crazy'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-the-great-khali.png', 'WWE: The Great Khali'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-the-miz.png', 'WWE: The Miz'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-tommy-dreamer.png', 'WWE: Tommy Dreamer'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-torrie-wilson.png', 'WWE: Torrie Wilson'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-triple-h.png', 'WWE: Triple H'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-umaga.png', 'WWE: Umaga'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-undertaker.png', 'WWE: Undertaker'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-big-daddy-v.png', 'WWE: Big Daddy V'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-shawn-michaels.png', 'WWE: Shawn Michaels'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-val-venis.png', 'WWE: Val Venis'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-victoria.png', 'WWE: Victoria'),
            $this->buildItemStructure($category, 'home-items/wwe-wwe-zack-ryder.png', 'WWE: Zack Ryder'),
        ];
    }
}
