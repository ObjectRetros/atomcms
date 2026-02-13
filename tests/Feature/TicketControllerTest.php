<?php

use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User;

beforeEach(function () {
    installHotel();
    $this->actingAs(User::factory()->create());
});

function createCategory(string $name = 'General'): WebsiteHelpCenterCategory
{
    return WebsiteHelpCenterCategory::create([
        'name' => $name . uniqid(),
        'content' => 'Test content',
    ]);
}

test('users can view ticket create page', function () {
    createCategory();

    $response = $this->get(route('help-center.ticket.create'));

    $response->assertStatus(200);
    $response->assertViewHas('categories');
    $response->assertViewHas('openTickets');
});

test('users can create a ticket', function () {
    $category = createCategory();

    $response = $this->post(route('help-center.ticket.store'), [
        'category_id' => $category->id,
        'title' => 'Help needed',
        'content' => 'I need help with something',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('website_help_center_tickets', [
        'user_id' => auth()->id(),
        'category_id' => $category->id,
    ]);
});

test('users can view their own ticket', function () {
    $category = createCategory();
    $ticket = WebsiteHelpCenterTicket::create([
        'user_id' => auth()->id(),
        'category_id' => $category->id,
        'title' => 'Test ticket',
        'content' => 'Test ticket',
        'open' => true,
    ]);

    $response = $this->get(route('help-center.ticket.show', $ticket));

    $response->assertStatus(200);
    $response->assertViewHas('ticket');
});

test('users cannot view others tickets without permission', function () {
    $otherUser = User::factory()->create();
    $category = createCategory();
    $ticket = WebsiteHelpCenterTicket::create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'title' => 'Test ticket',
        'content' => 'Test ticket',
        'open' => true,
    ]);

    $response = $this->get(route('help-center.ticket.show', $ticket));

    $response->assertForbidden();
});

test('users can delete their own ticket', function () {
    $category = createCategory();
    $ticket = WebsiteHelpCenterTicket::create([
        'user_id' => auth()->id(),
        'category_id' => $category->id,
        'title' => 'Test ticket',
        'content' => 'Test ticket',
        'open' => true,
    ]);

    $response = $this->delete(route('help-center.ticket.destroy', $ticket));

    $response->assertRedirect(route('me.show'));
    $this->assertDatabaseMissing('website_help_center_tickets', [
        'id' => $ticket->id,
    ]);
});

test('users cannot delete others tickets', function () {
    $otherUser = User::factory()->create();
    $category = createCategory();
    $ticket = WebsiteHelpCenterTicket::create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'title' => 'Test ticket',
        'content' => 'Test ticket',
        'open' => true,
    ]);

    $response = $this->delete(route('help-center.ticket.destroy', $ticket));

    $response->assertForbidden();
});

test('users can toggle ticket status', function () {
    $category = createCategory();
    $ticket = WebsiteHelpCenterTicket::create([
        'user_id' => auth()->id(),
        'category_id' => $category->id,
        'title' => 'Test ticket',
        'content' => 'Test ticket',
        'open' => true,
    ]);

    $response = $this->put(route('help-center.ticket.toggle-status', $ticket));

    $response->assertRedirect();
    expect($ticket->fresh()->open)->toBeFalse();
});
