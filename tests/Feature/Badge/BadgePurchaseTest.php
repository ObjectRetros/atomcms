<?php

use App\Actions\Badge\CreateDrawnBadge;
use App\Exceptions\BadgePurchaseException;
use App\Models\User;
use App\Models\WebsiteDrawBadge;
use Illuminate\Support\Facades\File;

function drawnBadgePayload(): array
{
    $image = imagecreatetruecolor(40, 40);

    ob_start();
    imagegif($image);
    $bytes = ob_get_clean();
    imagedestroy($image);

    return [
        'badge_data' => 'data:image/gif;base64,' . base64_encode($bytes),
        'badge_name' => 'My Badge',
        'badge_description' => 'A hand drawn badge',
    ];
}

function prepareBadgeDirectory(): string
{
    $directory = storage_path('framework/testing/badges');

    File::ensureDirectoryExists($directory);
    File::cleanDirectory($directory);

    setSetting('badge_path_filesystem', $directory);
    setSetting('drawbadge_currency_value', '150');
    setSetting('drawbadge_currency_type', 'credits');

    return $directory;
}

test('buying a drawn badge charges credits and stores the file', function () {
    installHotel();
    $directory = prepareBadgeDirectory();

    $user = User::factory()->create(['credits' => 1000]);

    $response = $this->actingAs($user)
        ->post(route('badge.buy'), drawnBadgePayload())
        ->assertOk()
        ->assertJson(['success' => true]);

    $badge = WebsiteDrawBadge::where('user_id', $user->id)->first();

    expect($badge)->not->toBeNull()
        ->and(file_exists($badge->badge_path))->toBeTrue()
        ->and($response->json('badge_url'))->toBe($badge->badge_url)
        ->and($response->json('badge_path_filesystem'))->toBeNull()
        ->and((int) $user->refresh()->credits)->toBe(850);
});

test('a buyer without enough credits is rejected and no file is left behind', function () {
    installHotel();
    $directory = prepareBadgeDirectory();

    $user = User::factory()->create(['credits' => 100]);

    $this->actingAs($user)
        ->post(route('badge.buy'), drawnBadgePayload())
        ->assertStatus(400)
        ->assertJson(['success' => false]);

    expect(WebsiteDrawBadge::count())->toBe(0)
        ->and(File::files($directory))->toBeEmpty()
        ->and((int) $user->refresh()->credits)->toBe(100);
});

test('invalid image data is rejected by validation', function () {
    installHotel();
    prepareBadgeDirectory();

    $user = User::factory()->create(['credits' => 1000]);

    $this->actingAs($user)
        ->post(route('badge.buy'), [
            'badge_data' => 'data:image/gif;base64,' . base64_encode('not a gif'),
            'badge_name' => 'My Badge',
            'badge_description' => 'A hand drawn badge',
        ])
        ->assertSessionHasErrors('badge_data');

    expect(WebsiteDrawBadge::count())->toBe(0)
        ->and((int) $user->refresh()->credits)->toBe(1000);
});

test('invalid image bytes cannot leave a partial badge or charge', function () {
    installHotel();
    $directory = prepareBadgeDirectory();
    $user = User::factory()->create(['credits' => 1000]);

    expect(fn () => app(CreateDrawnBadge::class)->execute($user, [
        'badge_data' => base64_encode('not a gif'),
        'badge_name' => 'Invalid',
        'badge_description' => 'Invalid',
    ]))->toThrow(BadgePurchaseException::class)
        ->and(File::files($directory))->toBeEmpty()
        ->and(WebsiteDrawBadge::count())->toBe(0)
        ->and((int) $user->refresh()->credits)->toBe(1000);
});
