<?php

use App\Models\User;
use App\Models\User\WebsiteUserGuestbook;

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create();
    $this->profileOwner = User::factory()->create(['username' => 'profileowner']);
});

test('users can post on another users profile', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('guestbook.store', $this->profileOwner), [
        'message' => 'Hello there!',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('website_user_guestbooks', [
        'profile_id' => $this->profileOwner->id,
        'user_id' => $this->user->id,
        'message' => 'Hello there!',
    ]);
});

test('users cannot post on their own profile', function () {
    $this->actingAs($this->profileOwner);

    $response = $this->post(route('guestbook.store', $this->profileOwner), [
        'message' => 'Hello myself!',
    ]);

    $response->assertForbidden();
});

test('users can delete their own guestbook message', function () {
    $this->actingAs($this->user);

    $guestbook = WebsiteUserGuestbook::create([
        'profile_id' => $this->profileOwner->id,
        'user_id' => $this->user->id,
        'message' => 'Test message',
    ]);

    $response = $this->delete(route('guestbook.destroy', [$this->profileOwner, $guestbook]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('website_user_guestbooks', [
        'id' => $guestbook->id,
    ]);
});

test('profile owner can delete guestbook messages', function () {
    $this->actingAs($this->profileOwner);

    $guestbook = WebsiteUserGuestbook::create([
        'profile_id' => $this->profileOwner->id,
        'user_id' => $this->user->id,
        'message' => 'Test message',
    ]);

    $response = $this->delete(route('guestbook.destroy', [$this->profileOwner, $guestbook]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('website_user_guestbooks', [
        'id' => $guestbook->id,
    ]);
});
