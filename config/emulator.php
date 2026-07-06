<?php

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Drivers\Arcturus\ArcturusCurrencyRepository;
use App\Emulator\Drivers\Arcturus\ArcturusPlayerStatsRepository;
use App\Emulator\Drivers\Plus\PlusCurrencyRepository;
use App\Emulator\Drivers\Plus\PlusPlayerStatsRepository;

return [

    /*
    |--------------------------------------------------------------------------
    | Emulator driver
    |--------------------------------------------------------------------------
    |
    | The emulator whose database schema this hotel runs on. Each driver maps
    | the CMS's domain concepts (currency, stats, ranks, ...) onto that
    | emulator's own tables and columns.
    |
    */

    'driver' => env('EMULATOR_DRIVER', 'arcturus'),

    /*
    |--------------------------------------------------------------------------
    | Driver bindings
    |--------------------------------------------------------------------------
    |
    | Each driver maps an emulator contract to the implementation that speaks
    | its schema. A new emulator only needs to implement the contracts and add
    | an entry here - no core changes.
    |
    */

    'drivers' => [

        'arcturus' => [
            CurrencyRepository::class => ArcturusCurrencyRepository::class,
            PlayerStatsRepository::class => ArcturusPlayerStatsRepository::class,
        ],

        'plus' => [
            CurrencyRepository::class => PlusCurrencyRepository::class,
            PlayerStatsRepository::class => PlusPlayerStatsRepository::class,
        ],

    ],

];
