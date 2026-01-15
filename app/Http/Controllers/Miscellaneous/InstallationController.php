<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\Miscellaneous\WebsiteSetting;
use App\Rules\ValidateInstallationKeyRule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InstallationController extends Controller
{
    public function index()
    {
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
        $settings = $this->getSettingsForStep((int) $currentStep);

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

        WebsiteSetting::where('key', 'theme')->update([
            'value' => 'atom',
        ]);

        return to_route('installation.index');
    }

    public function completeInstallation()
    {
        // Clear all caches before marking as complete
        Cache::forget('website_permissions');
        Cache::forget('website_settings');

        // Mark installation as complete
        WebsiteInstallation::latest()->first()->update([
            'completed' => true,
        ]);

        return to_route('welcome');
    }

    private function updateSettings(Request $request): void
    {
        foreach ($request->except('_token') as $key => $value) {
            WebsiteSetting::where('key', '=', $key)->update([
                'value' => $value ?? '',
            ]);
        }

        // Cache will be automatically cleared by WebsiteSetting model events
    }

    private function getSettingsForStep(int $step)
    {
        $settingsData = array_chunk(WebsiteSetting::all()->pluck('key')->toArray(), ceil(WebsiteSetting::count() / 4));

        $settings = match ($step) {
            1 => $settingsData[0] ?? [],
            2 => $settingsData[1] ?? [],
            3 => $settingsData[2] ?? [],
            4 => $settingsData[3] ?? [],
            5 => [], // Completion step has no settings
            default => throw new Exception('Step does not exist'),
        };

        return WebsiteSetting::query()
            ->whereIn('key', $settings)
            ->select(['key', 'value', 'comment'])
            ->get();
    }
}
