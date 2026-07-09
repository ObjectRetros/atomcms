<?php

use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User;

function createTicket(User $user): WebsiteHelpCenterTicket
{
    $category = WebsiteHelpCenterCategory::firstOrCreate(['name' => 'General'], ['content' => 'General questions']);

    return $user->tickets()->create([
        'category_id' => $category->id,
        'title' => 'Something is broken here',
        'content' => 'A long enough description of the problem.',
        'open' => true,
    ]);
}

beforeEach(function () {
    installHotel();
    $this->user = User::factory()->create();
});

test('a user can submit a ticket', function () {
    $category = WebsiteHelpCenterCategory::firstOrCreate(['name' => 'General'], ['content' => 'General questions']);

    $this->actingAs($this->user)
        ->post(route('help-center.ticket.store'), [
            'category_id' => $category->id,
            'title' => 'Something is broken here',
            'content' => 'A long enough description of the problem.',
        ])
        ->assertSessionHas('success');

    expect($this->user->tickets()->count())->toBe(1);
});

test('a user can view and close their own ticket', function () {
    $ticket = createTicket($this->user);

    $this->actingAs($this->user)->get(route('help-center.ticket.show', $ticket))->assertOk();

    $this->actingAs($this->user)
        ->put(route('help-center.ticket.toggle-status', $ticket))
        ->assertSessionHas('success');

    expect((bool) $ticket->refresh()->open)->toBeFalse();
});

test('a user cannot view someone else\'s ticket', function () {
    $ticket = createTicket($this->user);
    $stranger = User::factory()->create(['rank' => 1]);

    $this->actingAs($stranger)->get(route('help-center.ticket.show', $ticket))->assertForbidden();
});

test('a user can reply to their ticket', function () {
    $ticket = createTicket($this->user);

    $this->actingAs($this->user)
        ->post(route('help-center.ticket.reply.store', $ticket), [
            'content' => 'Some additional information here.',
        ])
        ->assertSessionHas('success');

    expect($ticket->replies()->count())->toBe(1);
});

test('the ticket overview is staff-only', function () {
    $this->actingAs($this->user)->get(route('help-center.ticket.index'))->assertForbidden();
});

test('a user can delete their own ticket', function () {
    $ticket = createTicket($this->user);

    $this->actingAs($this->user)
        ->delete(route('help-center.ticket.destroy', $ticket))
        ->assertSessionHas('success');

    expect($this->user->tickets()->count())->toBe(0);
});
