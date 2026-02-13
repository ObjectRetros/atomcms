<?php

namespace App\Services;

use App\Enums\CurrencyTypes;
use App\Exceptions\RconConnectionException;
use Illuminate\Support\Facades\Log;
use JsonException;
use Socket;

class RconService
{
    protected ?Socket $socket = null;

    public bool $isConnected = false;

    protected array $config = [];

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
        $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (! $this->socket) {
            $error = socket_strerror(socket_last_error());
            Log::error("RCON initialization failed: $error");

            $this->closeConnection();

            return;
        }

        if (! @socket_connect($this->socket, $this->config['ip'], $this->config['port'])) {
            $error = socket_strerror(socket_last_error());
            Log::error("RCON connection failed: $error");

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

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function sendCommand(string $command, ?array $data = null)
    {
        if (! $this->isConnected) {
            $error = 'RCON command failed: Not connected';
            Log::error($error);

            $this->closeConnection();

            return $this->isConnected;
        }

        $payload = json_encode(['key' => $command, 'data' => $data], JSON_THROW_ON_ERROR);

        if (! @socket_write($this->socket, $payload, strlen($payload))) {
            $error = socket_strerror(socket_last_error($this->socket));
            Log::error("RCON command ($command) failed: $error");

            $this->closeConnection();

            return $this->isConnected;
        }

        return $this->isConnected;
    }

    /**
     * @throws RconConnectionException|JsonException
     */
    public function sendGift($user, int $item_id, string $message = 'Here is a gift.'): void
    {
        $this->sendCommand('sendgift', [
            'user_id' => $user->id,
            'itemid' => $item_id,
            'message' => $message,
        ]);
    }

    /**
     * @throws RconConnectionException|JsonException
     */
    public function giveCredits($user, int $credits): void
    {
        $this->sendCommand('givecredits', [
            'user_id' => $user->id,
            'credits' => $credits,
        ]);
    }

    /**
     * @throws RconConnectionException|JsonException
     */
    public function giveBadge($user, string $badge): void
    {
        $this->sendCommand('givebadge', [
            'user_id' => $user->id,
            'badge' => $badge,
        ]);
    }

    /**
     * @throws RconConnectionException|JsonException
     */
    public function setMotto($user, string $motto): void
    {
        $this->sendCommand('setmotto', [
            'user_id' => $user->id,
            'motto' => $motto,
        ]);
    }

    /**
     * @throws RconConnectionException|JsonException
     */
    public function updateWordFilter(): void
    {
        $this->sendCommand('updatewordfilter');
    }

    /**
     * @throws RconConnectionException|JsonException
     */
    public function disconnectUser($user): void
    {
        $this->sendCommand('disconnect', [
            'user_id' => $user->id,
            'username' => $user->username,
        ]);
    }

    /**
     * @throws RconConnectionException|JsonException
     */
    public function givePoints($user, CurrencyTypes $type, int $amount): void
    {
        $this->sendCommand('givepoints', [
            'user_id' => $user->id,
            'points' => $amount,
            'type' => $type,
        ]);
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function giveGotw($user, int $amount): void
    {
        $this->givePoints($user, CurrencyTypes::Points, $amount);
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function giveDiamonds($user, int $amount): void
    {
        $this->givePoints($user, CurrencyTypes::Diamonds, $amount);
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function giveDuckets($user, int $amount): void
    {
        $this->givePoints($user, CurrencyTypes::Duckets, $amount);
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function setRank($user, int $rank): void
    {
        $this->sendCommand('setrank', [
            'user_id' => $user->id,
            'rank' => $rank,
        ]);
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function updateCatalog(): void
    {
        $this->sendCommand('updatecatalog');
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function alertUser($user, string $message): void
    {
        $this->sendCommand('alertuser', [
            'user_id' => $user->id,
            'message' => $message,
        ]);
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function forwardUser($user, int $roomId): void
    {
        $this->sendCommand('forwarduser', [
            'user_id' => $user->id,
            'room_id' => $roomId,
        ]);
    }

    /**
     * @throws RconConnectionException
     * @throws JsonException
     */
    public function updateConfig($user, string $command): void
    {
        $this->sendCommand('executecommand', [
            'user_id' => $user->id,
            'command' => $command,
        ]);
    }
}
