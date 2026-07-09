<?php

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Backfills the setting the badge page uses to locate the flash client's
     * external_flash_texts file on existing installations.
     */
    public function up(): void
    {
        WebsiteSetting::query()->firstOrCreate(['key' => 'flash_external_texts_file'], [
            'value' => '',
            'comment' => 'The path to the flash client external_flash_texts file, used by the badge page (leave empty when not serving the flash client)',
        ]);
    }

    public function down(): void
    {
        WebsiteSetting::query()->where('key', 'flash_external_texts_file')->delete();
    }
};
