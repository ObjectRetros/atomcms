<?php

use App\Providers\RouteServiceProvider;

test('the register routes follow the fortify naming convention', function () {
    installHotel();

    expect(route('register', absolute: false))->toBe('/register')
        ->and(route('register.store', absolute: false))->toBe('/register');

    $this->get(route('register'))->assertOk();
});

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
