<?php

namespace App\Services;

use App\Contracts\Rcon;
use App\Enums\CurrencyTypes;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Defers every RCON send until the surrounding database transaction commits,
 * so a rolled-back purchase never grants items in the emulator. Outside a
 * transaction the callback runs immediately, leaving normal calls unchanged.
 */
class AfterCommitRcon implements Rcon
{
    public function __construct(private readonly Rcon $inner) {}

    public function isConnected(): bool
    {
        return $this->inner->isConnected();
    }

    /**
     * The send is deferred, so the returned bool reflects the connection
     * state now rather than the eventual write result.
     *
     * @param  array<string, mixed>|null  $data
     */
    public function sendCommand(string $command, ?array $data = null): bool
    {
        $this->defer(fn () => $this->inner->sendCommand($command, $data));

        return $this->inner->isConnected();
    }

    public function sendGift(User $user, int $itemId, string $message = 'Here is a gift.'): void
    {
        $this->defer(fn () => $this->inner->sendGift($user, $itemId, $message));
    }

    public function giveCredits(User $user, int $credits): void
    {
        $this->defer(fn () => $this->inner->giveCredits($user, $credits));
    }

    public function giveBadge(User $user, string $badge): void
    {
        $this->defer(fn () => $this->inner->giveBadge($user, $badge));
    }

    public function setMotto(User $user, string $motto): void
    {
        $this->defer(fn () => $this->inner->setMotto($user, $motto));
    }

    public function updateWordFilter(): void
    {
        $this->defer(fn () => $this->inner->updateWordFilter());
    }

    public function disconnectUser(User $user): void
    {
        $this->defer(fn () => $this->inner->disconnectUser($user));
    }

    public function givePoints(User $user, CurrencyTypes $type, int $amount): void
    {
        $this->defer(fn () => $this->inner->givePoints($user, $type, $amount));
    }

    public function giveGotw(User $user, int $amount): void
    {
        $this->defer(fn () => $this->inner->giveGotw($user, $amount));
    }

    public function giveDiamonds(User $user, int $amount): void
    {
        $this->defer(fn () => $this->inner->giveDiamonds($user, $amount));
    }

    public function giveDuckets(User $user, int $amount): void
    {
        $this->defer(fn () => $this->inner->giveDuckets($user, $amount));
    }

    public function setRank(User $user, int $rank): void
    {
        $this->defer(fn () => $this->inner->setRank($user, $rank));
    }

    public function updateCatalog(): void
    {
        $this->defer(fn () => $this->inner->updateCatalog());
    }

    public function alertUser(User $user, string $message): void
    {
        $this->defer(fn () => $this->inner->alertUser($user, $message));
    }

    public function forwardUser(User $user, int $roomId): void
    {
        $this->defer(fn () => $this->inner->forwardUser($user, $roomId));
    }

    public function updateConfig(User $user, string $command): void
    {
        $this->defer(fn () => $this->inner->updateConfig($user, $command));
    }

    private function defer(callable $send): void
    {
        DB::afterCommit($send);
    }
}
