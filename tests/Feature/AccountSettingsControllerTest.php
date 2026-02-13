<?php

use App\Models\User;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create([
        'mail' => 'test@example.com',
        'motto' => 'Original motto',
    ]);
});

test('users can view account settings page', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('settings.account.show'));

    $response->assertStatus(200);
    $response->assertViewHas('user');
});

test('users can update their email', function () {
    $this->actingAs($this->user);

    $response = $this->put(route('settings.account.update'), [
        'mail' => 'newemail@example.com',
        'motto' => $this->user->motto,
    ]);

    $response->assertRedirect(route('settings.account.show'));
    $response->assertSessionHas('success');

    expect($this->user->fresh()->mail)->toBe('newemail@example.com');
});

test('guests cannot access account settings', function () {
    $response = $this->get(route('settings.account.show'));

    $response->assertRedirect(route('login'));
});

test('users can view session logs', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('settings.session-logs'));

    $response->assertStatus(200);
    $response->assertViewHas('logs');
});
