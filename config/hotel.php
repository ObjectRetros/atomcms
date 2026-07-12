<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hotel clients
    |--------------------------------------------------------------------------
    |
    | Which game clients the hotel serves. The badge page only maintains
    | external texts for enabled clients, and the flash paths tell it where
    | client assets live below the public directory.
    |
    */

    'client' => [

        'nitro' => [
            'enabled' => env('CLIENT_NITRO_ENABLED', true),
        ],

        'flash' => [
            'enabled' => env('CLIENT_FLASH_ENABLED', false),

            // Path to the flash client's files, relative to the public directory.
            'relative_files_path' => env('CLIENT_FLASH_FILES_PATH', 'client/flash'),
        ],

    ],

];
