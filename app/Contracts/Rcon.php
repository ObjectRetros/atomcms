<?php

namespace App\Contracts;

use App\Enums\CurrencyTypes;
use App\Models\User;

/**
 * Bridge to the Habbo emulator over RCON. Bound to RconService in production
 * and to FakeRcon under testing so the emulator socket is never required.
 */
interface Rcon
{
    public function isConnected(): bool;

    /**
     * @param  array<string, mixed>|null  $data
     */
    public function sendCommand(string $command, ?array $data = null): bool;

    public function sendGift(User $user, int $itemId, string $message = 'Here is a gift.'): void;

    public function giveCredits(User $user, int $credits): void;

    public function giveBadge(User $user, string $badge): void;

    public function setMotto(User $user, string $motto): void;

    public function updateWordFilter(): void;

    public function disconnectUser(User $user): void;

    public function givePoints(User $user, CurrencyTypes $type, int $amount): void;

    public function giveGotw(User $user, int $amount): void;

    public function giveDiamonds(User $user, int $amount): void;

    public function giveDuckets(User $user, int $amount): void;

    public function setRank(User $user, int $rank): void;

    public function updateCatalog(): void;

    public function alertUser(User $user, string $message): void;

    public function forwardUser(User $user, int $roomId): void;

    public function updateConfig(User $user, string $command): void;
}
