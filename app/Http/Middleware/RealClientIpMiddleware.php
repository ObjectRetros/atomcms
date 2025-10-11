<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RealClientIpMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $proxyHeaders = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_TRUE_CLIENT_IP',
        ];

        foreach ($proxyHeaders as $header) {
            if (! empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    [$ip] = explode(',', $ip);
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    // Set the real IP as REMOTE_ADDR
                    $request->server->set('REMOTE_ADDR', $ip);
                    break;
                }
            }
        }

        // Special handling for REMOTE_ADDR with multiple IPs
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        if (! empty($remoteAddr) && strpos($remoteAddr, ',') !== false) {
            [$ip] = explode(',', $remoteAddr);
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $request->server->set('REMOTE_ADDR', $ip);
            }
        }

        return $next($request);
    }
}
