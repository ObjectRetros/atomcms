<?php

namespace App\Http\Responses;

use App\Support\FrontendUrls;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AccessResponse
{
    public static function make(Request $request, string $code, string $message, int $status, string $destination, bool $withErrors = false): Response
    {
        // Livewire's persistent middleware stops hydration only for redirects.
        // Returning JSON here would let a restricted component action continue.
        if ($request->is('api/v1', 'api/v1/*') || ($request->expectsJson() && ! $request->hasHeader('X-Livewire'))) {
            return response()->json(['code' => $code, 'message' => __($message)], $status);
        }

        $response = redirect(app(FrontendUrls::class)->route($destination));

        return $withErrors ? $response->withErrors(['message' => __($message)]) : $response;
    }
}
