<?php

namespace App\Services;

use App\Contracts\Rcon;
use App\Data\RconResponse;
use App\Enums\CurrencyTypes;
use App\Exceptions\RconConnectionException;
use App\Models\User;

/**
 * Test double for Rcon: never opens a socket and records the calls it receives
 * so tests can assert on them. Disconnected by default, so services fall back
 * to their database path.
 */
class FakeRcon implements Rcon
{
    /**
     * @var list<array{method: string, args: array<string, mixed>}>
     */
    public array $calls = [];

    /**
     * How many times isConnected() was probed. Tracked separately from
     * $calls so existing call-sequence assertions stay unaffected.
     */
    public int $connectivityProbes = 0;

    /** @var list<RconResponse> */
    private array $responses = [];

    public function __construct(private bool $connected = false) {}

    public function connected(bool $connected = true): self
    {
        $this->connected = $connected;

        return $this;
    }

    public function respondWith(RconResponse ...$responses): self
    {
        array_push($this->responses, ...$responses);

        return $this;
    }

    public function isConnected(): bool
    {
        $this->connectivityProbes++;

        return $this->connected;
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public function sendCommand(string $command, ?array $data = null): RconResponse
    {
        $this->record(__FUNCTION__, ['command' => $command, 'data' => $data]);

        if (! $this->connected) {
            throw new RconConnectionException('Unable to connect to fake RCON');
        }

        $response = array_shift($this->responses) ?? new RconResponse(0, 'OK');

        if ($command === 'disconnect' && $response->successful() && isset($data['user_id'])) {
            User::whereKey((int) $data['user_id'])->update(['online' => '0']);
        }

        return $response;
    }

    public function sendGift(User $user, int $itemId, string $message = 'Here is a gift.'): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'itemId' => $itemId, 'message' => $message]);
    }

    public function giveCurrency(User $user, CurrencyTypes $currency, int $amount): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'currency' => $currency->name, 'amount' => $amount]);
    }

    public function giveBadge(User $user, string $badge): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'badge' => $badge]);
    }

    public function setMotto(User $user, string $motto): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'motto' => $motto]);
    }

    public function updateWordFilter(): void
    {
        $this->record(__FUNCTION__, []);
    }

    public function disconnectUser(User $user): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id]);
    }

    public function setRank(User $user, int $rank): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'rank' => $rank]);
    }

    public function updateCatalog(): void
    {
        $this->record(__FUNCTION__, []);
    }

    public function alertUser(User $user, string $message): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'message' => $message]);
    }

    public function forwardUser(User $user, int $roomId): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'roomId' => $roomId]);
    }

    public function updateConfig(User $user, string $command): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'command' => $command]);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    private function record(string $method, array $args): void
    {
        $this->calls[] = ['method' => $method, 'args' => $args];
    }
}
