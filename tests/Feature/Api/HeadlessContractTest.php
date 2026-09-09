<?php

use App\Models\Articles\WebsiteArticle;
use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\User;
use Symfony\Component\Process\Process;

beforeEach(function () {
    installHotel();
    config(['atom.mode' => 'headless']);
});

test('published contract accepts actual public private and mutation responses', function () {
    $user = User::factory()->create(['website_balance' => 1000]);
    $article = WebsiteArticle::create(['user_id' => $user->id, 'title' => 'Contract article', 'short_story' => 'Preview', 'full_story' => '<p>Content</p>', 'can_comment' => true]);
    $article->comments()->create(['user_id' => $user->id, 'comment' => 'Contract comment']);
    $samples = [];
    foreach (['bootstrap', 'status', 'users/' . $user->username, 'articles', 'articles/' . $article->slug, 'articles/' . $article->slug . '/comments'] as $path) {
        $response = $this->getJson('/api/v1/' . $path)->assertOk();
        $samples[] = ['method' => 'GET', 'path' => '/api/v1/' . $path, 'status' => $response->status(), 'body' => json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)];
    }

    $this->actingAs($user);
    foreach (['me', 'ban', 'shop', 'leaderboards', 'support', 'support/tickets', 'homes/' . $user->username] as $path) {
        $response = $this->getJson('/api/v1/' . $path)->assertOk();
        $samples[] = ['method' => 'GET', 'path' => '/api/v1/' . $path, 'status' => $response->status(), 'body' => json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)];
    }
    $package = makePackage();
    $path = '/api/v1/shop/packages/' . $package->id . '/purchases';
    $response = $this->postJson($path, [], ['Idempotency-Key' => 'contract-purchase'])->assertCreated();
    $samples[] = ['method' => 'POST', 'path' => $path, 'status' => $response->status(), 'body' => json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)];

    $category = WebsiteHelpCenterCategory::create(['name' => 'Help', 'content' => 'Ask a question']);
    $path = '/api/v1/support/tickets';
    $response = $this->postJson($path, ['category_id' => $category->id, 'title' => 'Contract question', 'content' => 'A detailed contract question'])->assertCreated();
    $samples[] = ['method' => 'POST', 'path' => $path, 'status' => $response->status(), 'body' => json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)];
    $path .= '/' . $response->json('data.id');
    $this->postJson($path . '/replies', ['content' => 'More contract details'])->assertCreated();
    $response = $this->getJson($path)->assertOk();
    $samples[] = ['method' => 'GET', 'path' => $path, 'status' => $response->status(), 'body' => json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)];

    $response = $this->putJson('/api/v1/me/password', ['password' => 'new-password'])->assertUnprocessable();
    $samples[] = ['method' => 'PUT', 'path' => '/api/v1/me/password', 'status' => $response->status(), 'body' => json_decode($response->getContent(), false, 512, JSON_THROW_ON_ERROR)];
    $process = new Process(['node', base_path('scripts/api/validate-response.mjs')], base_path());
    $process->setInput(json_encode($samples, JSON_THROW_ON_ERROR));
    $process->run();
    expect($process->isSuccessful())->toBeTrue($process->getErrorOutput() . $process->getOutput());
});
