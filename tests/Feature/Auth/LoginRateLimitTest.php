<?php

use App\Models\User;

test('login rate limits cannot be bypassed by changing username case', function () {
    installHotel();
    $user = User::factory()->create(['username' => 'CaseSensitiveUser']);

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), [
            'username' => strtolower($user->username),
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('username');
    }

    $this->post(route('login.store'), [
        'username' => strtoupper($user->username),
        'password' => 'password',
    ])->assertTooManyRequests();

    $this->assertGuest();
    $this->travel(61)->seconds();

    $this->post(route('login.store'), [
        'username' => strtoupper($user->username),
        'password' => 'password',
    ])->assertRedirect(route('me.show'));

    $this->assertAuthenticatedAs($user);
});

test('malformed login usernames fail validation without crashing the rate limiter', function () {
    installHotel();

    $this->postJson(route('login.store'), [
        'username' => ['unexpected' => 'value'],
        'password' => 'password',
    ])->assertUnprocessable()->assertJsonValidationErrors('username');

    $this->assertGuest();
});
