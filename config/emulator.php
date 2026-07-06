<?php

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Contracts\BanRepository;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\FurnitureRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Data\Feature;
use App\Emulator\Drivers\Arcturus\ArcturusBadgeRepository;
use App\Emulator\Drivers\Arcturus\ArcturusBanRepository;
use App\Emulator\Drivers\Arcturus\ArcturusCurrencyRepository;
use App\Emulator\Drivers\Arcturus\ArcturusFurnitureRepository;
use App\Emulator\Drivers\Arcturus\ArcturusPlayerStatsRepository;
use App\Emulator\Drivers\Plus\PlusBadgeRepository;
use App\Emulator\Drivers\Plus\PlusBanRepository;
use App\Emulator\Drivers\Plus\PlusCurrencyRepository;
use App\Emulator\Drivers\Plus\PlusFurnitureRepository;
use App\Emulator\Drivers\Plus\PlusPlayerStatsRepository;

return [

    /*
    |--------------------------------------------------------------------------
    | Emulator driver
    |--------------------------------------------------------------------------
    |
    | The emulator whose database schema this hotel runs on. Each driver maps
    | the CMS's domain concepts (currency, stats, badges, bans, furniture)
    | onto that emulator's own tables and columns.
    |
    */

    'driver' => env('EMULATOR_DRIVER', 'arcturus'),

    /*
    |--------------------------------------------------------------------------
    | Driver bindings and features
    |--------------------------------------------------------------------------
    |
    | bindings: maps each emulator contract to the implementation that speaks
    | the driver's schema. features: the Feature cases this driver supports -
    | anything absent is hidden from the site and housekeeping with an early
    | return. A new emulator only needs to implement the contracts and add an
    | entry here; implementing a Feature for a driver means porting the
    | screens that read its schema, then listing the case.
    |
    */

    'drivers' => [

        'arcturus' => [
            'bindings' => [
                BadgeRepository::class => ArcturusBadgeRepository::class,
                BanRepository::class => ArcturusBanRepository::class,
                CurrencyRepository::class => ArcturusCurrencyRepository::class,
                FurnitureRepository::class => ArcturusFurnitureRepository::class,
                PlayerStatsRepository::class => ArcturusPlayerStatsRepository::class,
            ],
            'features' => Feature::cases(),
        ],

        'plus' => [
            'bindings' => [
                BadgeRepository::class => PlusBadgeRepository::class,
                BanRepository::class => PlusBanRepository::class,
                CurrencyRepository::class => PlusCurrencyRepository::class,
                FurnitureRepository::class => PlusFurnitureRepository::class,
                PlayerStatsRepository::class => PlusPlayerStatsRepository::class,
            ],
            'features' => [],
        ],

    ],

];
