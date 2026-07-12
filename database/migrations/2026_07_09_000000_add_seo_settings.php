<?php

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Adds the settings both themes use to render SEO meta tags. When left
     * empty the themes fall back to a translated default based on the hotel name.
     */
    public function up(): void
    {
        WebsiteSetting::query()->firstOrCreate(['key' => 'seo_description'], [
            'value' => '',
            'comment' => 'The meta description shown in search engine results and social media embeds (leave empty to use a default based on the hotel name)',
        ]);

        WebsiteSetting::query()->firstOrCreate(['key' => 'seo_keywords'], [
            'value' => '',
            'comment' => 'Comma separated meta keywords for search engines (leave empty to use a default based on the hotel name)',
        ]);
    }

    public function down(): void
    {
        WebsiteSetting::query()->whereIn('key', ['seo_description', 'seo_keywords'])->delete();
    }
};
