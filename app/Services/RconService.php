<?php

namespace App\Services;

use App\Contracts\Rcon;
use App\Enums\CurrencyTypes;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Socket;

class RconService implements Rcon
{
    protected ?Socket $socket = null;

    protected bool $isConnected = false;

    /**
     * @var array{ip: mixed, port: int}
     */
    protected array $config;

    public function __construct()
    {
        $this->config = [
            'ip' => setting('rcon_ip'),
            'port' => (int) setting('rcon_port'),
        ];

        $this->initialize();
    }

    private function initialize(): void
    {
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($socket === false) {
            Log::error('RCON initialization failed: ' . socket_strerror(socket_last_error()));

            $this->closeConnection();

            return;
        }

        $this->socket = $socket;

        if (! @socket_connect($this->socket, $this->config['ip'], $this->config['port'])) {
            Log::error('RCON connection failed: ' . socket_strerror(socket_last_error()));

            $this->closeConnection();

            return;
        }

        $this->isConnected = true;
    }

    private function closeConnection(): void
    {
        if ($this->socket) {
            socket_close($this->socket);
        }

        $this->socket = null;
        $this->isConnected = false;
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function sendCommand(string $command, ?array $data = null): bool
    {
        if (! $this->isConnected) {
            Log::error('RCON command failed: Not connected');

            $this->closeConnection();

            return false;
        }

        $payload = json_encode(['key' => $command, 'data' => $data], JSON_THROW_ON_ERROR);

        if (! @socket_write($this->socket, $payload, strlen($payload))) {
            Log::error("RCON command ($command) failed: " . socket_strerror(socket_last_error($this->socket)));

            $this->closeConnection();

            return false;
        }

        return true;
    }

    public function sendGift(User $user, int $itemId, string $message = 'Here is a gift.'): void
    {
        $this->sendCommand('sendgift', [
            'user_id' => $user->id,
            'itemid' => $itemId,
            'message' => $message,
        ]);
    }

    public function giveCurrency(User $user, CurrencyTypes $currency, int $amount): void
    {
        if ($currency === CurrencyTypes::Credits) {
            $this->sendCommand('givecredits', [
                'user_id' => $user->id,
                'credits' => $amount,
            ]);

            return;
        }

        $this->sendCommand('givepoints', [
            'user_id' => $user->id,
            'points' => $amount,
            'type' => $currency,
        ]);
    }

    public function giveBadge(User $user, string $badge): void
    {
        $this->sendCommand('givebadge', [
            'user_id' => $user->id,
            'badge' => $badge,
        ]);
    }

    public function setMotto(User $user, string $motto): void
    {
        $this->sendCommand('setmotto', [
            'user_id' => $user->id,
            'motto' => $motto,
        ]);
    }

    public function updateWordFilter(): void
    {
        $this->sendCommand('updatewordfilter');
    }

    public function disconnectUser(User $user): void
    {
        $this->sendCommand('disconnect', [
            'user_id' => $user->id,
            'username' => $user->username,
        ]);
    }

    public function setRank(User $user, int $rank): void
    {
        $this->sendCommand('setrank', [
            'user_id' => $user->id,
            'rank' => $rank,
        ]);
    }

    public function updateCatalog(): void
    {
        $this->sendCommand('updatecatalog');
    }

    public function alertUser(User $user, string $message): void
    {
        $this->sendCommand('alertuser', [
            'user_id' => $user->id,
            'message' => $message,
        ]);
    }

    public function forwardUser(User $user, int $roomId): void
    {
        $this->sendCommand('forwarduser', [
            'user_id' => $user->id,
            'room_id' => $roomId,
        ]);
    }

    public function updateConfig(User $user, string $command): void
    {
        $this->sendCommand('executecommand', [
            'user_id' => $user->id,
            'command' => $command,
        ]);
    }
}
