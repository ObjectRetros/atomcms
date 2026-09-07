<?php

use App\Livewire\ArticleComments;
use App\Livewire\ArticleReactions;
use App\Models\Articles\WebsiteArticle;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    installHotel();
    setSetting('max_comment_per_article', '50');

    $this->reader = User::factory()->create();
    $this->article = WebsiteArticle::create([
        'user_id' => $this->reader->id,
        'title' => 'Shared article interaction limits',
        'short_story' => 'The same limits apply to all clients.',
        'full_story' => '<p>The same limits apply to all clients.</p>',
        'image' => 'articles/limits.png',
        'can_comment' => true,
    ]);
    $this->actingAs($this->reader);
});

test('comment limits are shared between HTTP and Livewire and recover after a minute', function () {
    foreach (range(1, 9) as $attempt) {
        $this->post(route('article.comment.store', $this->article->slug), ['comment' => "HTTP comment {$attempt}"])
            ->assertSessionHas('success');
    }

    Livewire::test(ArticleComments::class, ['article' => $this->article])
        ->set('comment', 'The tenth comment uses Livewire.')
        ->call('postComment')
        ->assertHasNoErrors();

    $this->postJson(route('article.comment.store', $this->article->slug), ['comment' => 'Too many comments.'])
        ->assertTooManyRequests()
        ->assertHeader('Retry-After');

    Livewire::test(ArticleComments::class, ['article' => $this->article])
        ->set('comment', 'Livewire also reached the limit.')
        ->call('postComment')
        ->assertStatus(429);

    expect($this->article->comments()->count())->toBe(10);

    $this->travel(61)->seconds();

    Livewire::test(ArticleComments::class, ['article' => $this->article])
        ->set('comment', 'Allowed after the limit expires.')
        ->call('postComment')
        ->assertHasNoErrors();

    expect($this->article->comments()->count())->toBe(11);
});

test('reaction limits are shared between HTTP and Livewire and recover after a minute', function () {
    foreach (range(1, 29) as $attempt) {
        $this->postJson(route('article.toggle-reaction', $this->article->slug), ['reaction' => 'like'])->assertOk();
    }

    Livewire::test(ArticleReactions::class, ['article' => $this->article])
        ->call('toggleReaction', 'like')
        ->assertHasNoErrors();

    $this->postJson(route('article.toggle-reaction', $this->article->slug), ['reaction' => 'like'])
        ->assertTooManyRequests()
        ->assertHeader('Retry-After');

    Livewire::test(ArticleReactions::class, ['article' => $this->article])
        ->call('toggleReaction', 'like')
        ->assertStatus(429);

    expect($this->article->reactions()->count())->toBe(0);

    $otherReader = User::factory()->create();
    $this->flushSession()->actingAs($otherReader)
        ->postJson(route('article.toggle-reaction', $this->article->slug), ['reaction' => 'like'])
        ->assertOk();

    $this->flushSession()->actingAs($this->reader);
    $this->travel(61)->seconds();

    Livewire::test(ArticleReactions::class, ['article' => $this->article])
        ->call('toggleReaction', 'like')
        ->assertHasNoErrors();

    expect($this->article->reactions()->where('user_id', $this->reader->id)->count())->toBe(1);
});
