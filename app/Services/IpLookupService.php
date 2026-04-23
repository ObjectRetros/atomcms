<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IpLookupService
{
    private string $baseUrl = 'https://api.ipdata.co';

    public function ipLookup(string $ip, string $apiKey): array
    {
        $response = Http::acceptJson()
            ->connectTimeout(3)
            ->timeout(8)
            ->get(sprintf('%s/%s?api-key=%s', $this->baseUrl, $ip, $apiKey));

        if (! $response->ok()) {
            $payload = $response->json();
            $message = is_array($payload) && array_key_exists('message', $payload) ? $payload['message'] : 'Unknown error';

            return [
                'message' => $message,
                'status' => $response->status(),
            ];
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : [];
    }
}
