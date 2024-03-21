<?php

namespace App\Http\Controllers;

use App\Exceptions\MigrationFailedException;
use App\Models\WebsiteInstallation;
use App\Models\WebsiteSetting;
use App\Rules\ValidateInstallationKeyRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InstallationController extends Controller
{
    public function index()
    {
        $this->migrateAndSeed();

        return view('installation.index');
    }

    public function storeInstallationKey(Request $request)
    {
        $request->validate([
            'installation_key' => ['required', 'string', 'max:255', new ValidateInstallationKeyRule],
        ]);

        WebsiteInstallation::first()->update([
            'step' => 1,
            'user_ip' => $request->ip(),
        ]);

        return to_route('installation.show-step', 1);
    }

    public function showStep($currentStep)
    {
        $settings = $this->getSettingsForStep((int)$currentStep);

        return view('installation.step-' . $currentStep, [
            'settings' => $settings,
        ]);
    }

    public function saveStepSettings(Request $request)
    {
        $this->updateSettings($request);

        WebsiteInstallation::increment('step');

        return to_route('installation.show-step', WebsiteInstallation::first()->step);
    }

    public function previousStep()
    {
        WebsiteInstallation::decrement('step');

        return to_route('installation.show-step', WebsiteInstallation::first()->step);
    }

    public function restartInstallation()
    {
        WebsiteInstallation::first()->update([
            'step' => 0,
            'installation_key' => Str::uuid(),
            'user_ip' => null,
        ]);

        return to_route('installation.index');
    }

    public function completeInstallation()
    {
        $installation = WebsiteInstallation::increment('step');
        $installation->update([
            'completed' => true,
        ]);

        return to_route('welcome');
    }

    private function migrateAndSeed()
    {
        try {
            Artisan::call('migrate --seed');
        } catch (MigrationFailedException $e) {
            Log::error('Migration or seeding failed: ' . $e->getMessage());

            abort(500, 'An error occurred while trying to migrate');
        }
    }

    private function updateSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            WebsiteSetting::where('key', '=', $key)->update([
                'value' => $value
            ]);
        }
    }

    private function getSettingsForStep(int $step)
    {
        $settings = match ($step) {
            1 => ['theme', 'hotel_name', 'rcon_ip', 'rcon_port', 'avatar_imager', 'discord_invitation_link', 'discord_widget_id'],
            2 => ['start_motto', 'start_credits', 'start_duckets', 'start_diamonds', 'start_points', 'start_look', 'max_accounts_per_ip'],
            3 => ['referrals_needed', 'referral_reward_amount', 'min_staff_rank', 'maintenance_message', 'requires_beta_code', 'disable_registration', 'cms_color_mode'],
            4 => ['give_hc_on_register', 'hc_on_register_duration', 'max_comment_per_article', 'website_wordfilter_enabled', 'vpn_block_enabled', 'ipdata_api_key', 'housekeeping_url'],
            5 => [], // Completion step has no settings
            default => throw new \Exception('Step does not exist'),
        };

        return WebsiteSetting::query()
            ->whereIn('key', $settings)
            ->select(['key', 'value', 'comment'])
            ->get();
    }
}
