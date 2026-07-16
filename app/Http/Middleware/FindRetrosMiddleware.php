<?php

namespace App\Http\Middleware;

use App\Services\FindRetrosService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/* Credits to Kani for this */
class FindRetrosMiddleware
{
    public function __construct(private readonly FindRetrosService $findRetros) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (config('habbo.findretros.enabled') && ! $this->findRetros->checkHasVoted($request)) {
            return redirect($this->findRetros->getRedirectUri());
        }

        return $next($request);
    }
}
