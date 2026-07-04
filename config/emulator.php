<?php

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Drivers\Arcturus\ArcturusCurrencyRepository;

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
        ],

        // 'plus' => [
        //     CurrencyRepository::class => \App\Emulator\Drivers\Plus\PlusCurrencyRepository::class,
        // ],

    ],

];
