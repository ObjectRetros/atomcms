<?php

use App\Models\User;

test('the flash client page renders and refreshes the session ticket', function () {
    installHotel();

    $user = User::factory()->create(['auth_ticket' => '']);

    $this->actingAs($user)
        ->get(route('flash-client'))
        ->assertOk();

    expect($user->fresh()->auth_ticket)->not->toBe('');
});
