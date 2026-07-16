<?php

use App\Models\Articles\WebsiteArticle;
use App\Models\User;

function publishArticle(User $author): WebsiteArticle
{
    return WebsiteArticle::create([
        'user_id' => $author->id,
        'title' => 'Grand opening',
        'short_story' => 'We are open!',
        'full_story' => '<p>Welcome to the hotel, everyone.</p>',
        'image' => 'articles/opening.png',
    ]);
}

beforeEach(function () {
    installHotel();

    $this->author = User::factory()->create();
    $this->reader = User::factory()->create();
});

test('an article page renders', function () {
    $article = publishArticle($this->author);

    $this->actingAs($this->reader)
        ->get(route('article.show', $article->slug))
        ->assertOk()
        ->assertSee('Grand opening');
});

test('articles with the same title receive unique slugs', function () {
    $firstArticle = publishArticle($this->author);
    $secondArticle = publishArticle($this->author);

    expect($firstArticle->slug)->toBe('grand-opening')
        ->and($secondArticle->slug)->toBe('grand-opening-1');
});

test('a reader can comment and remove their own comment', function () {
    $article = publishArticle($this->author);

    $this->actingAs($this->reader)
        ->post(route('article.comment.store', $article->slug), ['comment' => 'Congratulations on the launch!'])
        ->assertSessionHas('success');

    $comment = $article->comments()->first();
    expect($comment)->not->toBeNull();

    $this->actingAs($this->reader)
        ->delete(route('article.comment.destroy', $comment))
        ->assertSessionHas('success');

    expect($article->comments()->count())->toBe(0);
});

test('a reader cannot delete someone else\'s comment', function () {
    setSetting('min_staff_rank', '5');

    $article = publishArticle($this->author);

    $this->actingAs($this->reader)
        ->post(route('article.comment.store', $article->slug), ['comment' => 'Congratulations on the launch!']);

    $comment = $article->comments()->first();
    $stranger = User::factory()->create(['rank' => 1]);

    $this->actingAs($stranger)
        ->delete(route('article.comment.destroy', $comment))
        ->assertSessionHasErrors();

    expect($article->comments()->count())->toBe(1);
});

test('a reader can toggle a reaction', function () {
    $article = publishArticle($this->author);

    $this->actingAs($this->reader)
        ->post(route('article.toggle-reaction', $article->slug), ['reaction' => 'like'])
        ->assertOk();

    expect($article->reactions()->count())->toBe(1);

    $this->actingAs($this->reader)
        ->post(route('article.toggle-reaction', $article->slug), ['reaction' => 'like'])
        ->assertOk();

    expect($article->reactions()->count())->toBe(0);
});
