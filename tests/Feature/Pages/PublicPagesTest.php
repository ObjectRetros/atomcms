<?php

use App\Models\User;

beforeEach(function () {
    installHotel();
});

test('public community pages render', function (string $route) {
    $this->get(route($route))->assertOk();
})->with([
    'articles index' => 'article.index',
    'rules' => 'help-center.rules.index',
]);

test('public responses include browser security headers', function () {
    $this->get(route('article.index'))
        ->assertOk()
        ->assertHeader('Content-Security-Policy', "base-uri 'self'; frame-ancestors 'self'")
        ->assertHeader('Permissions-Policy', 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeaderMissing('Strict-Transport-Security');
});

test('secure production responses enable transport security', function () {
    $this->app->detectEnvironment(fn () => 'production');

    $this->withHeader('X-Forwarded-Proto', 'https')
        ->get(route('article.index'))
        ->assertOk()
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
});

test('authenticated community pages render', function (string $route) {
    $this->actingAs(User::factory()->create())
        ->get(route($route))
        ->assertOk();
})->with([
    'photos' => 'photos.index',
    'staff' => 'staff.index',
    'teams' => 'teams.index',
    'staff applications' => 'staff-applications.index',
    'team applications' => 'team-applications.index',
    'help center' => 'help-center.index',
    'ticket create' => 'help-center.ticket.create',
]);

test('a user home page renders', function () {
    $user = User::factory()->create();
    $subject = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home.show', $subject->username))
        ->assertOk();
});

test('switching language stores the locale', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('me.show'))
        ->get(route('language.select', 'en'))
        ->assertRedirect();

    expect(session('locale'))->toBe('en');
});
