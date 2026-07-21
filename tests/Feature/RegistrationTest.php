<?php

use App\Providers\RouteServiceProvider;

test('new users can register', function () {
    $response = $this->post('/register', [
        'username' => 'Test_User',
        'mail' => 'test@example.com',
        'password' => 'Sup3rSecret!',
        'password_confirmation' => 'Sup3rSecret!',
        'terms' => true,
    ]);

    expect(auth()->check())->toBeTrue()
        ->and($response->status())->toBe(302)
        ->and(parse_url($response->headers->get('Location'), PHP_URL_PATH))->toBe(parse_url(RouteServiceProvider::HOME, PHP_URL_PATH));
});
