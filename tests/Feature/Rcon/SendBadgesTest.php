<?php

use App\Actions\SendBadges;
use App\Emulator\Contracts\BadgeRepository;
use App\Models\User;

test('sending multiple badges probes rcon connectivity once', function () {
    $user = User::factory()->create();
    $this->rcon->connected();

    app(SendBadges::class)->execute($user, 'BDG1;BDG2;BDG3');

    expect($this->rcon->connectivityProbes)->toBe(1)
        ->and(array_column($this->rcon->calls, 'method'))->toBe(['giveBadge', 'giveBadge', 'giveBadge']);
});

test('badges fall back to the database when rcon is offline', function () {
    $user = User::factory()->create();

    app(SendBadges::class)->execute($user, 'OFF1;OFF2');

    expect($this->rcon->connectivityProbes)->toBe(1)
        ->and($this->rcon->calls)->toBe([])
        ->and(app(BadgeRepository::class)->codes($user))->toContain('OFF1', 'OFF2');
});
