<?php

use App\Models\User;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create();
});

test('account pages render for the signed-in user', function (string $route) {
    $this->actingAs($this->user)->get(route($route))->assertOk();
})->with([
    'account settings' => 'settings.account.show',
    'password settings' => 'settings.password.show',
    'two factor settings' => 'settings.two-factor',
    'session logs' => 'settings.session-logs',
    'draw badge' => 'draw-badge',
]);

test('the logo generator is permission gated', function () {
    $this->actingAs(User::factory()->create(['rank' => 1]))
        ->get(route('logo-generator.index'))
        ->assertRedirect(route('me.show'));

    $this->actingAs(User::factory()->create(['rank' => 7]))
        ->get(route('logo-generator.index'))
        ->assertOk();
});

test('guests are redirected off account pages', function () {
    $this->get(route('settings.account.show'))->assertRedirect();
});
