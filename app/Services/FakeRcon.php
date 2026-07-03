<?php

namespace App\Services;

use App\Contracts\Rcon;
use App\Enums\CurrencyTypes;
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

    public function __construct(private bool $connected = false) {}

    public function connected(bool $connected = true): self
    {
        $this->connected = $connected;

        return $this;
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public function sendCommand(string $command, ?array $data = null): bool
    {
        return $this->record(__FUNCTION__, ['command' => $command, 'data' => $data]);
    }

    public function sendGift(User $user, int $itemId, string $message = 'Here is a gift.'): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'itemId' => $itemId, 'message' => $message]);
    }

    public function giveCredits(User $user, int $credits): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'credits' => $credits]);
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

    public function givePoints(User $user, CurrencyTypes $type, int $amount): void
    {
        $this->record(__FUNCTION__, ['user' => $user->id, 'type' => $type->value, 'amount' => $amount]);
    }

    public function giveGotw(User $user, int $amount): void
    {
        $this->givePoints($user, CurrencyTypes::Points, $amount);
    }

    public function giveDiamonds(User $user, int $amount): void
    {
        $this->givePoints($user, CurrencyTypes::Diamonds, $amount);
    }

    public function giveDuckets(User $user, int $amount): void
    {
        $this->givePoints($user, CurrencyTypes::Duckets, $amount);
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
    private function record(string $method, array $args): bool
    {
        $this->calls[] = ['method' => $method, 'args' => $args];

        return $this->connected;
    }
}
