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

    public function showStep(int $currentStep): View|RedirectResponse
    {
        // A resubmitted form, a stale tab or a hand-typed URL can ask for a
        // step either side of the wizard. Send them to the nearest real one
        // rather than showing them an exception.
        if ($currentStep !== $this->clamp($currentStep)) {
            return to_route('installation.show-step', $this->clamp($currentStep));
        }

        /** @var view-string $view */
        $view = "installation.step-{$currentStep}";

        return view($view, [
            'settings' => $this->getSettingsForStep($currentStep),
        ]);
    }

    public function saveStepSettings(Request $request): RedirectResponse
    {
        $installation = $this->installation();

        $this->updateSettings($request, (int) $installation->step);

        $installation->update(['step' => $this->clamp($installation->step + 1)]);

        return to_route('installation.show-step', $installation->step);
    }

    public function previousStep(): RedirectResponse
    {
        $installation = $this->installation();
        $installation->update(['step' => $this->clamp($installation->step - 1)]);

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

    private function clamp(int $step): int
    {
        return max(WebsiteInstallation::FIRST_STEP, min(WebsiteInstallation::LAST_STEP, $step));
    }

    /**
     * The wizard splits every setting into SETTING_STEPS equal chunks; the
     * final step is the completion screen and carries none.
     *
     * @return Collection<int, WebsiteSetting>
     */
    private function getSettingsForStep(int $step): Collection
    {
        $keys = WebsiteSetting::query()->orderBy('id')->pluck('key')->all();

        $chunkSize = max(1, (int) ceil(count($keys) / WebsiteInstallation::SETTING_STEPS));
        $chunks = array_chunk($keys, $chunkSize);

        // The completion step carries no settings, and so does any chunk the
        // split did not produce.
        $settings = $chunks[$step - 1] ?? [];

        return WebsiteSetting::query()
            ->whereIn('key', $settings)
            ->orderBy('id')
            ->select(['id', 'key', 'value', 'comment'])
            ->get();
    }
}
