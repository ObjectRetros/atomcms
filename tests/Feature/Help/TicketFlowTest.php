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

test('a user cannot reply to another users ticket', function () {
    $ticket = createTicket($this->user);
    $stranger = User::factory()->create(['rank' => 1]);

    $this->actingAs($stranger)
        ->post(route('help-center.ticket.reply.store', $ticket), [
            'content' => 'I should not be able to post this reply.',
        ])
        ->assertForbidden();

    expect($ticket->replies()->count())->toBe(0);
});

test('a user cannot reply to a closed ticket', function () {
    $ticket = createTicket($this->user);
    $ticket->update(['open' => false]);

    $this->actingAs($this->user)
        ->post(route('help-center.ticket.reply.store', $ticket), [
            'content' => 'I should not be able to post this reply.',
        ])
        ->assertForbidden();

    expect($ticket->replies()->count())->toBe(0);
});

test('a user cannot delete another users reply', function () {
    $ticket = createTicket($this->user);
    $reply = $ticket->replies()->create([
        'user_id' => $this->user->id,
        'content' => 'This reply belongs to the ticket owner.',
    ]);
    $stranger = User::factory()->create(['rank' => 1]);

    $this->actingAs($stranger)
        ->delete(route('help-center.ticket.reply.destroy', $reply))
        ->assertForbidden();

    expect($reply->fresh())->not->toBeNull();
});

test('authorized staff can view and reply to another users ticket', function () {
    $ticket = createTicket($this->user);
    $staff = User::factory()->create(['rank' => 7]);

    $this->actingAs($staff)
        ->get(route('help-center.ticket.show', $ticket))
        ->assertOk();

    $this->actingAs($staff)
        ->post(route('help-center.ticket.reply.store', $ticket), [
            'content' => 'A staff response to the ticket owner.',
        ])
        ->assertSessionHas('success');

    expect($ticket->replies()->where('user_id', $staff->id)->exists())->toBeTrue();
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
