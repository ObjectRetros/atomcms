<?php

namespace App\Http\Middleware;

use App\Exceptions\MigrationFailedException;
use App\Models\Miscellaneous\WebsiteInstallation;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $this->ensureInstallationTableExists();

        $installation = $this->getInstallation();

        if ($installation && $installation->completed && $request->is('installation*')) {
            return to_route('welcome');
        }

        $isInstallationStepHandled = $this->handleInstallationSteps($request, $installation);

        if (!$isInstallationStepHandled) {
            return $this->redirectIfNotCompleted($installation);
        }

        return $next($request);
    }

    private function ensureInstallationTableExists()
    {
        if (!Schema::hasTable('website_installation')) {
            Artisan::call("migrate", ['--path' => "database/migrations/" . findMigration('website_installation')]);

            if (!Schema::hasTable('website_installation')) {
                throw new MigrationFailedException('website_installation');
            }
        }

        if (!Schema::hasTable('sessions')) {
            Artisan::call("migrate", ['--path' => "database/migrations/" . findMigration('sessions')]);
        }
    }

    private function getInstallation()
    {
        try {
            $installation = WebsiteInstallation::query()->first();

            if (!$installation) {
                return WebsiteInstallation::create([
                    'step' => 0,
                    'completed' => false,
                    'installation_key' => Str::uuid(),
                    'user_ip' => request()?->ip(),
                ]);
            }

            return $installation;
        } catch (Exception $e) {
            Log::error('Error fetching or creating WebsiteInstallation: ' . $e->getMessage());
            abort(500, 'An error occurred while setting up the installation.');
        }
    }

    private function handleInstallationSteps(Request $request, WebsiteInstallation $installation)
    {
        if ($installation->completed) {
            return true;
        }

        if ($this->isWelcomeStep($request, $installation)) {
            return true;
        }

        if ($this->isRedirectToWelcome($request, $installation)) {
            return false;
        }

        if ($this->isInvalidAccess($request, $installation)) {
            abort(403);
        }

        if ($this->isInvalidStep($request)) {
            return false;
        }

        if ($this->isMismatchedStep($request, $installation)) {
            return false;
        }

        return true;
    }

    private function isWelcomeStep(Request $request, WebsiteInstallation $installation)
    {
        return $installation->step === 0 && $request->getRequestUri() === '/installation';
    }

    private function isRedirectToWelcome(Request $request, WebsiteInstallation $installation)
    {
        return $installation->step === 0 && $request->getRequestUri() !== '/installation' && $request->method() !== 'POST';
    }

    private function isInvalidAccess(Request $request, WebsiteInstallation $installation)
    {
        return $installation->step > 0 && $request->ip() !== $installation->user_ip;
    }

    private function isInvalidStep(Request $request)
    {
        return !$this->isValidStep($request) && $this->isNonPostRequest($request);
    }

    private function isMismatchedStep(Request $request, WebsiteInstallation $installation)
    {
        return $this->getCurrentStep($request) !== $installation->step && $this->isNonPostRequest($request);
    }

    private function isValidStep(Request $request)
    {
        $step = $this->getCurrentStep($request);
        return filter_var($step, FILTER_VALIDATE_INT) !== false;
    }

    private function isNonPostRequest(Request $request)
    {
        return $request->method() !== 'POST' || $request->is('restart-installation');
    }

    private function getCurrentStep(Request $request)
    {
        return (int)Str::after($request->path(), 'step/');
    }

    private function redirectToStep(int $step)
    {
        return to_route('installation.show-step', $step);
    }

    protected function redirectIfNotCompleted(WebsiteInstallation $installation)
    {

        if ($installation->step === 0) {
            return to_route('installation.index');
        }

        return $this->redirectToStep($installation->step ?: 1);
    }
}
