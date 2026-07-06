<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Notifies the configured Discord webhook about a new registration. Dispatched
 * after the response so the Discord round-trip never delays sign-up.
 */
class SendRegisteredUserWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $username,
        private readonly string $ip,
        private readonly string $email,
    ) {}

    public function handle(): void
    {
        $url = setting('discord_webhook_url');

        if (! $url) {
            Log::error('Discord webhook url not provided', ['Please provide a discord webhook url before being able to send any webhook requests.']);

            return;
        }

        $response = Http::asJson()->post($url, [
            'username' => sprintf('%s Bot', setting('hotel_name')),
            'content' => "User: {$this->username} has just registered, with the IP: {$this->ip} and E-mail: {$this->email}",
        ]);

        if (! $response->successful()) {
            Log::error('Failed to send Discord webhook notification', [
                'username' => $this->username,
                'ip' => $this->ip,
                'email' => $this->email,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);
        }
    }
}
