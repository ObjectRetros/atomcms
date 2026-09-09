<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final class InstallationSetup
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        $rules = [
            'hotel_name' => ['sometimes', 'required', 'string', 'max:100'],
            'cms_color_mode' => ['sometimes', 'in:light,dark'],
        ];
        foreach (['start_credits', 'start_duckets', 'start_diamonds', 'start_points', 'max_accounts_per_ip', 'max_comment_per_article'] as $key) {
            $rules[$key] = ['sometimes', 'integer', 'min:0', 'max:2147483647'];
        }
        foreach (['google_recaptcha_enabled', 'cloudflare_turnstile_enabled', 'website_wordfilter_enabled', 'requires_beta_code', 'disable_registration', 'give_hc_on_register'] as $key) {
            $rules[$key] = ['sometimes', 'in:0,1'];
        }

        return $rules;
    }

    /** @param array<string, mixed> $settings */
    public function configure(array $settings): void
    {
        Validator::make(['settings' => $settings], ['settings' => ['array:' . implode(',', array_keys($this->rules()))]])->validate();
        $values = Validator::make($settings, $this->rules())->validate();
        DB::transaction(function () use ($values): void {
            foreach ($values as $key => $value) {
                WebsiteSetting::where('key', $key)->update(['value' => (string) $value]);
            }
        });
        app(SettingsService::class)->refresh();
    }

    public function complete(): void
    {
        DB::transaction(function (): void {
            WebsiteInstallation::query()->firstOrCreate([], ['step' => WebsiteInstallation::LAST_STEP, 'installation_key' => Str::uuid()->toString()]);
            WebsiteInstallation::query()->update(['completed' => true]);
        });
        Cache::forget('installation_record');
        Cache::forget('website_permissions');
        app(SettingsService::class)->refresh();
        InstallationService::setComplete();
        app()->forgetInstance(InstallationService::class);
    }
}
