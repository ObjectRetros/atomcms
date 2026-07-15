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
