<?php

use App\Models\User;

beforeEach(function () {
    installHotel();
});

test('users can view a profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.show', $user->username));

    $response->assertStatus(200);
    $response->assertViewHas('user');
    $response->assertViewHas('friends');
    $response->assertViewHas('groups');
    $response->assertViewHas('guestbook');
    $response->assertViewHas('photos');
});

test('profile shows correct user data', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'motto' => 'Test motto',
    ]);

    $response = $this->actingAs($user)->get(route('profile.show', 'testuser'));

    $response->assertStatus(200);
    $response->assertSee('testuser');
    $response->assertSee('Test motto');
});

test('profile returns 404 for non-existent user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.show', 'nonexistent'));

    $response->assertStatus(404);
});
