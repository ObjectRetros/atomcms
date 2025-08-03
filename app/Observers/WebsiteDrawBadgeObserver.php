<?php

namespace App\Observers;

use App\Models\WebsiteDrawBadge;
use Illuminate\Support\Facades\DB;

class WebsiteDrawBadgeObserver
{
    public function updated(WebsiteDrawBadge $websiteDrawBadge): void
    {
        if ($websiteDrawBadge->wasChanged('published')) {
            $badgeCode = pathinfo($websiteDrawBadge->badge_path, PATHINFO_FILENAME);

            if ($websiteDrawBadge->published) {
                $exists = DB::table('users_badges')
                    ->where('user_id', $websiteDrawBadge->user_id)
                    ->where('badge_code', $badgeCode)
                    ->exists();

                if (!$exists) {
                    DB::table('users_badges')->insert([
                        'user_id' => $websiteDrawBadge->user_id,
                        'slot_id' => 0,
                        'badge_code' => $badgeCode,
                    ]);
                }

                // Add to JSON
                $this->updateExternalTexts(true, $badgeCode, $websiteDrawBadge->badge_name, $websiteDrawBadge->badge_desc);
            } else {
                DB::table('users_badges')
                    ->where('user_id', $websiteDrawBadge->user_id)
                    ->where('badge_code', $badgeCode)
                    ->delete();

                // Remove from JSON
                $this->updateExternalTexts(false, $badgeCode);
            }
        }
    }

    protected function updateExternalTexts(bool $add, string $badgeCode, ?string $name = null, ?string $desc = null): void
    {
        $filePath = DB::table('website_settings')->where('key', 'nitro_external_texts_file')->value('value');

        if (!$filePath || !file_exists($filePath) || !is_writable($filePath)) {
            // Optionally log error
            return;
        }

        $json = json_decode(file_get_contents($filePath), true);

        if ($add) {
            $json = array_merge($json, [
                "badge_name_{$badgeCode}" => $name,
                "badge_desc_{$badgeCode}" => $desc
            ]);
        } else {
            unset($json["badge_name_{$badgeCode}"]);
            unset($json["badge_desc_{$badgeCode}"]);
        }

        file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}