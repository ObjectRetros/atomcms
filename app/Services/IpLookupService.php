<?php

namespace App\Services;

use App\Support\OutboundHttp;
use Illuminate\Http\Client\ConnectionException;

class IpLookupService
{
    private string $baseUrl = 'https://api.ipdata.co';

    /**
     * @return array<string, mixed>
     */
    public function ipLookup(string $ip, string $apiKey): array
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) === false) {
            return ['message' => 'Invalid IP address.', 'status' => 422];
        }

        try {
            $response = OutboundHttp::request()
                ->acceptJson()
                ->get($this->baseUrl . '/' . rawurlencode($ip), ['api-key' => $apiKey]);
        } catch (ConnectionException) {
            return ['message' => 'IP reputation service unavailable.', 'status' => 503];
        }

        if (! $response->ok()) {
            $message = $response->json('message');

            return [
                'message' => is_string($message) ? $message : 'Unknown error',
                'status' => $response->status(),
            ];
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : ['message' => 'Invalid response.', 'status' => 502];
    }
}
