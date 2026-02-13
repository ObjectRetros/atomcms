<?php

use App\Models\User;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create(['website_balance' => 1000]);
});

test('users can view shop index', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('shop.index'));

    $response->assertStatus(200);
    $response->assertViewHas('articles');
    $response->assertViewHas('categories');
});

test('guests cannot purchase packages', function () {
    $response = $this->get(route('shop.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view shop', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('shop.index'));

    $response->assertStatus(200);
});
