<?php

namespace App\Services;

use App\Contracts\Rcon;
use App\Data\RconResponse;
use App\Enums\CurrencyTypes;
use App\Exceptions\RconConnectionException;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use JsonException;

class RconService implements Rcon
{
    public function __construct(
        private readonly ?string $host = null,
        private readonly ?int $port = null,
    ) {}

    public function isConnected(): bool
    {
        try {
            $socket = $this->connect();
            fclose($socket);

            return true;
        } catch (RconConnectionException) {
            return false;
        }
    }

    public function sendCommand(string $command, ?array $data = null): RconResponse
    {
        $payload = json_encode(['key' => $command, 'data' => $data], JSON_THROW_ON_ERROR);
        $socket = $this->connect();

        try {
            $this->write($socket, $payload);
            stream_socket_shutdown($socket, STREAM_SHUT_WR);

            $response = stream_get_contents($socket);
            $metadata = stream_get_meta_data($socket);

            if ($metadata['timed_out']) {
                throw new RconConnectionException("RCON command '{$command}' timed out waiting for a response");
            }

            if ($response === false || trim($response) === '') {
                throw new RconConnectionException("RCON command '{$command}' returned an empty response");
            }

            return $this->parseResponse($command, $response);
        } finally {
            fclose($socket);
        }
    }

    /**
     * Arcturus accepts one JSON request per TCP connection and closes the
     * connection after writing its response.
     *
     * @return resource
     */
    private function connect()
    {
        $errorCode = 0;
        $errorMessage = '';
        $timeout = max(0.1, (float) config('habbo.rcon.connect_timeout_seconds', 1));
        $socket = @stream_socket_client(
            $this->endpoint(),
            $errorCode,
            $errorMessage,
            $timeout,
            STREAM_CLIENT_CONNECT,
        );

        if ($socket === false) {
            throw new RconConnectionException("Unable to connect to RCON: {$errorMessage} ({$errorCode})");
        }

        $readTimeout = max(0.1, (float) config('habbo.rcon.read_timeout_seconds', 2));
        $seconds = (int) $readTimeout;
        $microseconds = (int) (($readTimeout - $seconds) * 1_000_000);
        stream_set_timeout($socket, $seconds, $microseconds);

        return $socket;
    }

    private function endpoint(): string
    {
        $host = trim($this->host ?? (string) setting('rcon_ip'));
        $port = $this->port ?? (int) setting('rcon_port');

        if ($host === '' || $port < 1 || $port > 65535) {
            throw new RconConnectionException('RCON host or port is not configured correctly');
        }

        $formattedHost = str_contains($host, ':') && ! str_starts_with($host, '[')
            ? "[{$host}]"
            : $host;

        return "tcp://{$formattedHost}:{$port}";
    }

    /**
     * @param  resource  $socket
     */
    private function write($socket, string $payload): void
    {
        $written = 0;
        $length = strlen($payload);

        while ($written < $length) {
            $bytes = fwrite($socket, substr($payload, $written));

            if ($bytes === false || $bytes === 0) {
                throw new RconConnectionException('RCON connection closed before the command was fully written');
            }

            $written += $bytes;
        }
    }

    private function parseResponse(string $command, string $response): RconResponse
    {
        try {
            $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RconConnectionException("RCON command '{$command}' returned malformed JSON", previous: $exception);
        }

        if (
            ! is_array($decoded)
            || ! isset($decoded['status'], $decoded['message'])
            || ! is_int($decoded['status'])
            || ! is_string($decoded['message'])
        ) {
            throw new RconConnectionException("RCON command '{$command}' returned an invalid response");
        }

        return new RconResponse($decoded['status'], $decoded['message']);
    }

    /**
     * Typed RCON helpers are fire-and-forget operations. Preserve that contract
     * while retaining transport and emulator failures in the application log.
     *
     * @param  array<string, mixed>|null  $data
     */
    private function dispatchCommand(string $command, ?array $data = null): void
    {
        try {
            $response = $this->sendCommand($command, $data);

            if (! $response->successful()) {
                Log::warning('RCON command was rejected by the emulator', [
                    'command' => $command,
                    'status' => $response->status,
                    'message' => $response->message,
                ]);
            }
        } catch (RconConnectionException $exception) {
            Log::error('RCON command failed', [
                'command' => $command,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function sendGift(User $user, int $itemId, string $message = 'Here is a gift.'): void
    {
        $this->dispatchCommand('sendgift', [
            'user_id' => $user->id,
            'itemid' => $itemId,
            'message' => $message,
        ]);
    }

    public function giveCurrency(User $user, CurrencyTypes $currency, int $amount): void
    {
        if ($currency === CurrencyTypes::Credits) {
            $this->dispatchCommand('givecredits', [
                'user_id' => $user->id,
                'credits' => $amount,
            ]);

            return;
        }

        $this->dispatchCommand('givepoints', [
            'user_id' => $user->id,
            'points' => $amount,
            'type' => $currency,
        ]);
    }

    public function giveBadge(User $user, string $badge): void
    {
        $this->dispatchCommand('givebadge', [
            'user_id' => $user->id,
            'badge' => $badge,
        ]);
    }

    public function setMotto(User $user, string $motto): void
    {
        $this->dispatchCommand('setmotto', [
            'user_id' => $user->id,
            'motto' => $motto,
        ]);
    }

    public function updateWordFilter(): void
    {
        $this->dispatchCommand('updatewordfilter');
    }

    public function disconnectUser(User $user): void
    {
        $this->dispatchCommand('disconnect', [
            'user_id' => $user->id,
            'username' => $user->username,
        ]);
    }

    public function setRank(User $user, int $rank): void
    {
        $this->dispatchCommand('setrank', [
            'user_id' => $user->id,
            'rank' => $rank,
        ]);
    }

    public function updateCatalog(): void
    {
        $this->dispatchCommand('updatecatalog');
    }

    public function alertUser(User $user, string $message): void
    {
        $this->dispatchCommand('alertuser', [
            'user_id' => $user->id,
            'message' => $message,
        ]);
    }

    public function forwardUser(User $user, int $roomId): void
    {
        $this->dispatchCommand('forwarduser', [
            'user_id' => $user->id,
            'room_id' => $roomId,
        ]);
    }

    public function updateConfig(User $user, string $command): void
    {
        $this->dispatchCommand('executecommand', [
            'user_id' => $user->id,
            'command' => $command,
        ]);
    }
}
