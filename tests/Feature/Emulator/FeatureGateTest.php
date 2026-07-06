<?php

use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Models\User;

test('feature support follows the configured driver', function () {
    config(['emulator.driver' => 'arcturus']);

    expect(Emulator::supports(Feature::RareValues))->toBeTrue()
        ->and(Emulator::supports(Feature::RoomChatlogs))->toBeTrue();

    config(['emulator.driver' => 'plus']);

    expect(Emulator::supports(Feature::RareValues))->toBeFalse()
        ->and(Emulator::supports(Feature::RoomChatlogs))->toBeFalse();
});

test('gated routes early-return on drivers without the feature', function () {
    installHotel();

    $user = User::factory()->create();

    $this->actingAs($user)->get(route('values.index'))->assertOk();

    config(['emulator.driver' => 'plus']);

    $this->actingAs($user)
        ->get(route('values.index'))
        ->assertRedirect(route('welcome'));
});
