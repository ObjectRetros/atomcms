<?php

namespace App\Http\Middleware;

use App\Services\FindRetrosService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/* Credits to Kani for this */
class FindRetrosMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $findRetrosService = new FindRetrosService;

        if (config('habbo.findretros.enabled') && ! $findRetrosService->checkHasVoted()) {
            return redirect($findRetrosService->getRedirectUri());
        }

        return $next($request);
    }
}
