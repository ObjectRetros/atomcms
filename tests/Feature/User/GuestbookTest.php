<?php

use App\Models\User;

beforeEach(function () {
    installHotel();

    $this->owner = User::factory()->create();
    $this->visitor = User::factory()->create();
});

test('a visitor can sign a guestbook', function () {
    $this->actingAs($this->visitor)
        ->post(route('guestbook.store', $this->owner), ['message' => 'Nice profile!'])
        ->assertSessionHas('success');

    expect($this->owner->profileGuestbook()->count())->toBe(1);
});

test('the author can delete their own entry', function () {
    $entry = $this->owner->profileGuestbook()->create([
        'user_id' => $this->visitor->id,
        'message' => 'Nice profile!',
    ]);

    $this->actingAs($this->visitor)
        ->delete(route('guestbook.destroy', [$this->owner, $entry]))
        ->assertSessionHas('success');

    expect($this->owner->profileGuestbook()->count())->toBe(0);
});

test('the profile owner can remove entries from their guestbook', function () {
    $entry = $this->owner->profileGuestbook()->create([
        'user_id' => $this->visitor->id,
        'message' => 'Nice profile!',
    ]);

    $this->actingAs($this->owner)
        ->delete(route('guestbook.destroy', [$this->owner, $entry]))
        ->assertSessionHas('success');

    expect($this->owner->profileGuestbook()->count())->toBe(0);
});

test('a stranger cannot delete someone else\'s entry', function () {
    setSetting('min_staff_rank', '5');

    $entry = $this->owner->profileGuestbook()->create([
        'user_id' => $this->visitor->id,
        'message' => 'Nice profile!',
    ]);

    $stranger = User::factory()->create(['rank' => 1]);

    $this->actingAs($stranger)
        ->delete(route('guestbook.destroy', [$this->owner, $entry]))
        ->assertSessionHasErrors();

    expect($this->owner->profileGuestbook()->count())->toBe(1);
});
