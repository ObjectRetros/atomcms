<?php

namespace App\Services\Badge;

use App\Services\Network\PublicIpResolver;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class RemoteBadgeImageFetcher
{
    private const MAX_BYTES = 262_144;

    public function __construct(private readonly PublicIpResolver $resolver) {}

    public function fetch(string $url): string
    {
        $parts = parse_url($url);

        if (
            ! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || ! isset($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || (int) ($parts['port'] ?? 443) !== 443
        ) {
            throw new RuntimeException('Badge images must use a standard HTTPS URL without credentials.');
        }

        $host = strtolower($parts['host']);
        $address = $this->resolver->resolveIpv4($host);

        try {
            $response = Http::connectTimeout(2)
                ->timeout(5)
                ->withOptions([
                    'allow_redirects' => false,
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
                        CURLOPT_PROXY => '',
                        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
                        CURLOPT_RESOLVE => ["{$host}:443:{$address}"],
                    ],
                    'progress' => function (int $downloadTotal, int $downloadedBytes, int $uploadTotal, int $uploadedBytes): void {
                        if ($downloadTotal > self::MAX_BYTES || $downloadedBytes > self::MAX_BYTES) {
                            throw new RuntimeException('The remote badge image is too large.');
                        }
                    },
                ])
                ->accept('image/gif, image/png, image/jpeg')
                ->get($url);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('The remote badge image could not be downloaded.', previous: $exception);
        } catch (Throwable $exception) {
            throw $exception instanceof RuntimeException
                ? $exception
                : new RuntimeException('The remote badge image could not be downloaded.', previous: $exception);
        }

        if (! $response->successful() || strlen($response->body()) > self::MAX_BYTES) {
            throw new RuntimeException('The remote badge image could not be downloaded.');
        }

        return $response->body();
    }
}
