<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IpLookupService
{
    private string $baseUrl = 'https://api.ipdata.co';

    public function __construct(private string $apiKey) {}

    public function ipLookup(string $ip)
    {
        $response = Http::acceptJson()->get(sprintf('%s/%s?api-key=%s', $this->baseUrl, $ip, $this->apiKey));

        if (! $response->ok()) {
            $message = array_key_exists('message', $response->json()) ? $response->json()['message'] : 'Unknown error';

            return [
                'message' => $message,
                'status' => $response->status(),
            ];
        }

        return $response->json();
    }
}
