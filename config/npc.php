<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the OpenAI API connection for NPC chat functionality.
    |
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 150),
        'temperature' => env('OPENAI_TEMPERATURE', 0.8),
    ],

    /*
    |--------------------------------------------------------------------------
    | NPC API Security
    |--------------------------------------------------------------------------
    |
    | A shared secret between the Arcturus emulator plugin and this CMS.
    | The emulator plugin must send this key in the X-NPC-Token header.
    |
    */
    'api_token' => env('NPC_API_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | NPC Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for NPC behavior.
    |
    */
    'defaults' => [
        'interaction_distance' => env('NPC_INTERACTION_DISTANCE', 1), // Tiles
        'conversation_timeout' => env('NPC_CONVERSATION_TIMEOUT', 300), // Seconds
        'max_history_messages' => env('NPC_MAX_HISTORY', 20),
        'system_prompt' => env('NPC_SYSTEM_PROMPT', 'Sen bir otel lobisinde duran yardımsever ve samimi bir NPC\'sin. Adın Atlas. Oyunculara yardım edersin, sohbet edersin ve onları eğlendirirsin. Kısa ve öz cevaplar ver (maksimum 2-3 cümle). Türkçe konuş.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | NPC Bot Configuration for Room 208
    |--------------------------------------------------------------------------
    |
    | Configuration for the AI NPC bot placed in room 208.
    |
    */
    'bot' => [
        'name' => env('NPC_BOT_NAME', 'Atlas'),
        'motto' => env('NPC_BOT_MOTTO', 'Merhaba! Benimle sohbet et.'),
        'figure' => env('NPC_BOT_FIGURE', 'hr-3163-45.hd-180-1.ch-3030-65.lg-275-76.sh-3016-92.ha-1004-1408'),
        'gender' => env('NPC_BOT_GENDER', 'M'),
        'room_id' => env('NPC_BOT_ROOM_ID', 208),
        'x' => env('NPC_BOT_X', 13),
        'y' => env('NPC_BOT_Y', 13),
        'z' => env('NPC_BOT_Z', 0.0),
    ],
];
