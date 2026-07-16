<?php

namespace App\Jobs;

use App\Support\DiscordWebhookUrl;
use App\Support\OutboundHttp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Notifies the configured Discord webhook about a new registration. Dispatched
 * after the response so the Discord round-trip never delays sign-up.
 */
class SendRegisteredUserWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [10, 60, 300];

    public function __construct(
        private readonly string $username,
        private readonly string $ip,
        private readonly string $email,
    ) {}

    public function handle(): void
    {
        $url = setting('discord_webhook_url');

        if (! DiscordWebhookUrl::isValid($url)) {
            Log::error('Discord registration webhook URL is missing or invalid.');

            return;
        }

        try {
            $response = OutboundHttp::request()
                ->asJson()
                ->post($url, [
                    'username' => sprintf('%s Bot', setting('hotel_name')),
                    'content' => "User: {$this->username} has just registered, with the IP: {$this->ip} and E-mail: {$this->email}",
                ]);
        } catch (Throwable $exception) {
            Log::warning('Discord registration webhook delivery failed.', [
                'exception_class' => $exception::class,
            ]);

            throw new RuntimeException('Discord registration webhook delivery failed.');
        }

        if (! $response->successful()) {
            Log::warning('Discord registration webhook returned an error.', [
                'status' => $response->status(),
            ]);

            throw new RuntimeException('Discord registration webhook delivery failed.');
        }
    }
}
