<?php

use App\Filament\Resources\Atom\Articles\Pages\EditArticle;
use App\Models\Articles\WebsiteArticle;
use App\Models\User;
use App\Models\User\Ban;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

function articleComponentSnapshot(string $html, string $component): string
{
    preg_match_all('/wire:snapshot="([^"]+)"/', $html, $matches);

    foreach ($matches[1] as $encodedSnapshot) {
        $snapshot = html_entity_decode($encodedSnapshot, ENT_QUOTES | ENT_HTML5);

        if (json_decode($snapshot, true, flags: JSON_THROW_ON_ERROR)['memo']['name'] === $component) {
            return $snapshot;
        }
    }

    throw new RuntimeException("The page did not render the {$component} component.");
}

beforeEach(function () {
    installHotel();
    setSetting('force_staff_2fa', '0');
    setSetting('maintenance_enabled', '0');
    grantHousekeepingPermission('can_access_housekeeping', 6);
    grantHousekeepingPermission('write_article', 6);
    grantHousekeepingPermission('edit_article', 6);

    $this->staff = User::factory()->create(['rank' => 6]);
    $this->article = WebsiteArticle::create([
        'user_id' => $this->staff->id,
        'title' => 'Livewire access gates',
        'short_story' => 'Access is checked on every request.',
        'full_story' => '<p>Access is checked on every request.</p>',
        'image' => 'articles/access.png',
        'can_comment' => true,
    ]);

    $this->actingAs($this->staff);
});

test('livewire actions honor access restrictions introduced after page load', function (string $gate, string $redirect, string $surface) {
    $housekeeping = $surface === 'housekeeping';
    $page = $this->get($housekeeping
        ? '/housekeeping/website/articles/' . $this->article->id . '/edit'
        : route('article.show', $this->article->slug))->assertOk();
    $snapshot = articleComponentSnapshot($page->getContent(), $housekeeping ? Livewire::new(EditArticle::class)->getName() : 'article-comments');

    if ($gate === 'ban') {
        Ban::create([
            'user_id' => $this->staff->id,
            'ip' => '',
            'machine_id' => '',
            'user_staff_id' => $this->staff->id,
            'timestamp' => time(),
            'ban_expire' => time() + 3600,
            'ban_reason' => 'Restricted after opening the page',
            'type' => 'account',
        ]);
        Cache::forget('ban_verdict:user:' . $this->staff->id);
    } elseif ($gate === 'maintenance') {
        setSetting('maintenance_enabled', '1');
        setSetting('min_maintenance_login_rank', '7');
    } else {
        setSetting('force_staff_2fa', '1');
        setSetting('min_staff_rank', '4');
    }

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => $snapshot,
            'updates' => $housekeeping ? ['data.title' => 'This must not be saved.'] : ['comment' => 'This must not be posted.'],
            'calls' => [['method' => $housekeeping ? 'save' : 'postComment', 'params' => []]],
        ]],
    ], ['X-Livewire' => 'true'])->assertRedirect(route($redirect));

    expect($this->article->comments()->count())->toBe(0);
    expect($this->article->fresh()->title)->toBe('Livewire access gates');
})->with([
    'account ban' => ['ban', 'banned.show'],
    'maintenance' => ['maintenance', 'maintenance.show'],
    'staff two-factor requirement' => ['two-factor', 'settings.two-factor'],
])->with(['article', 'housekeeping']);

test('eligible readers can post through the article livewire update endpoint', function () {
    $page = $this->get(route('article.show', $this->article->slug))->assertOk();

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => articleComponentSnapshot($page->getContent(), 'article-comments'),
            'updates' => ['comment' => 'Still allowed to comment.'],
            'calls' => [['method' => 'postComment', 'params' => []]],
        ]],
    ], ['X-Livewire' => 'true'])->assertOk();

    expect($this->article->comments()->sole()->comment)->toBe('Still allowed to comment.');
});

test('eligible staff can save through the housekeeping livewire update endpoint', function () {
    $page = $this->get('/housekeeping/website/articles/' . $this->article->id . '/edit')->assertOk();

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => articleComponentSnapshot($page->getContent(), Livewire::new(EditArticle::class)->getName()),
            'updates' => ['data.title' => 'Still allowed to edit.'],
            'calls' => [['method' => 'save', 'params' => []]],
        ]],
    ], ['X-Livewire' => 'true'])->assertOk();

    expect($this->article->fresh()->title)->toBe('Still allowed to edit.');
});
