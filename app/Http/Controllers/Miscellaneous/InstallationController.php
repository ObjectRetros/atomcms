<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\Miscellaneous\WebsiteSetting;
use App\Rules\ValidateInstallationKeyRule;
use App\Services\InstallationService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InstallationController extends Controller
{
    public function index(): View
    {
        return view('installation.index');
    }

    public function storeInstallationKey(Request $request): RedirectResponse
    {
        $request->validate([
            'installation_key' => ['required', 'string', 'max:255', new ValidateInstallationKeyRule],
        ]);

        $this->installation()->update([
            'step' => 1,
            'user_ip' => $request->ip(),
        ]);

        return to_route('installation.show-step', 1);
    }

    public function showStep(int $currentStep): View
    {
        $settings = $this->getSettingsForStep($currentStep);
        $view = match ($currentStep) {
            1 => 'installation.step-1',
            2 => 'installation.step-2',
            3 => 'installation.step-3',
            4 => 'installation.step-4',
            5 => 'installation.step-5',
            default => throw new Exception('Step does not exist'),
        };

        return view($view, [
            'settings' => $settings,
        ]);
    }

    public function saveStepSettings(Request $request): RedirectResponse
    {
        $this->updateSettings($request);

        $installation = $this->installation();
        $installation->increment('step');

        return to_route('installation.show-step', $installation->step);
    }

    public function previousStep(): RedirectResponse
    {
        $installation = $this->installation();
        $installation->decrement('step');

        return to_route('installation.show-step', $installation->step);
    }

    public function restartInstallation(): RedirectResponse
    {
        $this->installation()->update([
            'step' => 0,
            'installation_key' => Str::uuid(),
            'user_ip' => null,
        ]);

        WebsiteSetting::where('key', 'theme')->update([
            'value' => 'atom',
        ]);

        return to_route('installation.index');
    }

    public function completeInstallation(): RedirectResponse
    {
        // Clear all caches before marking as complete
        Cache::forget('website_permissions');
        Cache::forget('website_settings');

        // Concurrent first-ever requests can each have created an installation
        // row; the wizard progressed on the oldest while completion previously
        // marked only the newest, leaving the row every check reads incomplete
        // forever. Mark them all so every reader agrees.
        WebsiteInstallation::query()->update([
            'completed' => true,
        ]);

        InstallationService::setComplete();

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

    private function installation(): WebsiteInstallation
    {
        return WebsiteInstallation::query()->oldest('id')->firstOrFail();
    }

    /** @return Collection<int, WebsiteSetting> */
    private function getSettingsForStep(int $step): Collection
    {
        $chunkSize = max(1, (int) ceil(WebsiteSetting::count() / 4));
        $settingsData = array_chunk(WebsiteSetting::all()->pluck('key')->toArray(), $chunkSize);

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
