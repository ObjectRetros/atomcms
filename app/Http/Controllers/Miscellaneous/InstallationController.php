<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\Miscellaneous\WebsiteSetting;
use App\Rules\ValidateInstallationKeyRule;
use App\Services\InstallationService;
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
            default => abort(404),
        };

        return view($view, [
            'settings' => $settings,
        ]);
    }

    public function saveStepSettings(Request $request): RedirectResponse
    {
        $installation = $this->installation();

        $this->updateSettings($request, (int) $installation->step);

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

    /**
     * Only the keys that belong to the step being saved may be written, and
     * only with scalar string values; everything else in the request payload
     * is ignored.
     */
    private function updateSettings(Request $request, int $step): void
    {
        $allowedKeys = $this->getSettingsForStep($step)->pluck('key');

        foreach ($request->except('_token') as $key => $value) {
            if (! $allowedKeys->contains($key) || (! is_string($value) && $value !== null)) {
                continue;
            }

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

    /**
     * The wizard splits all settings into four equal steps; the fifth step is
     * the completion screen and carries none.
     *
     * @return Collection<int, WebsiteSetting>
     */
    private function getSettingsForStep(int $step): Collection
    {
        if ($step < 1 || $step > 5) {
            abort(404);
        }

        $settings = WebsiteSetting::query()
            ->select(['id', 'key', 'value', 'comment'])
            ->orderBy('id')
            ->get();

        if ($step === 5) {
            return new Collection;
        }

        $chunkSize = max(1, (int) ceil($settings->count() / 4));

        /** @var Collection<int, WebsiteSetting> */
        return $settings->chunk($chunkSize)->values()->get($step - 1, new Collection)->values();
    }
}
