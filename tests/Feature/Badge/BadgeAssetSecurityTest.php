<?php

use App\Services\Badge\BadgeImageNormalizer;
use App\Services\Badge\BadgeImageStorage;
use App\Services\Badge\RemoteBadgeImageFetcher;
use App\Services\Network\PublicIpResolver;
use App\Support\BadgeCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function badgeImageBytes(string $format = 'png', int $width = 40, int $height = 40): string
{
    $image = imagecreatetruecolor($width, $height);
    ob_start();

    match ($format) {
        'gif' => imagegif($image),
        'jpeg' => imagejpeg($image),
        default => imagepng($image),
    };

    $bytes = ob_get_clean();
    imagedestroy($image);

    return (string) $bytes;
}

test('badge codes cannot traverse the badge directory', function () {
    expect(fn () => BadgeCode::filename('../outside'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => BadgeCode::filename('SAFE_CODE-1'))->not->toThrow(InvalidArgumentException::class)
        ->and(fn () => BadgeCode::filename('100000_' . str_repeat('A', 26)))->not->toThrow(InvalidArgumentException::class)
        ->and(fn () => BadgeCode::filename(str_repeat('A', 65)))->toThrow(InvalidArgumentException::class);
});

test('remote badge images reject non-https addresses before sending', function () {
    Http::fake();
    $fetcher = app(RemoteBadgeImageFetcher::class);

    expect(fn () => $fetcher->fetch('http://127.0.0.1/internal.gif'))
        ->toThrow(RuntimeException::class);

    Http::assertNothingSent();
});

test('remote badge images reject private addresses before sending', function () {
    Http::fake();

    expect(fn () => app(RemoteBadgeImageFetcher::class)->fetch('https://127.0.0.1/internal.gif'))
        ->toThrow(RuntimeException::class);

    Http::assertNothingSent();
});

test('the public IP resolver rejects private addresses', function () {
    expect(fn () => app(PublicIpResolver::class)->resolveIpv4('127.0.0.1'))
        ->toThrow(RuntimeException::class);
});

test('remote badge images are fetched once and normalized to a 40x40 gif', function () {
    $resolver = Mockery::mock(PublicIpResolver::class);
    $resolver->shouldReceive('resolveIpv4')->once()->with('assets.example.com')->andReturn('93.184.216.34');
    Http::fake([
        'https://assets.example.com/badge.png' => Http::response(badgeImageBytes(), 200, ['Content-Type' => 'image/png']),
    ]);

    $fetcher = new RemoteBadgeImageFetcher($resolver);
    $gif = app(BadgeImageNormalizer::class)->toGif($fetcher->fetch('https://assets.example.com/badge.png'));
    $info = getimagesizefromstring($gif);

    expect($info)->not->toBeFalse()
        ->and($info['mime'])->toBe('image/gif')
        ->and($info[0])->toBe(40)
        ->and($info[1])->toBe(40);

    Http::assertSentCount(1);
});

test('badge storage rejects invalid dimensions and never writes outside its disk', function () {
    Storage::fake('badges');
    $storage = app(BadgeImageStorage::class);

    expect(fn () => $storage->store('BADGE', badgeImageBytes(width: 41)))
        ->toThrow(RuntimeException::class);

    expect(Storage::disk('badges')->allFiles())->toBeEmpty();

    $storage->store('BADGE', badgeImageBytes());

    Storage::disk('badges')->assertExists('BADGE.gif');

    expect(fileperms(Storage::disk('badges')->path('BADGE.gif')) & 0777)->toBe(0644);
});
