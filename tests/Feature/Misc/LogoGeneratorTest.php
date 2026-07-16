<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    installHotel();
});

test('staff can upload a generated logo', function () {
    $staff = User::factory()->create(['rank' => 7]);

    $this->actingAs($staff)
        ->post(route('store.generated-logo'), [
            'logo' => UploadedFile::fake()->image('logo.png', 100, 40),
        ])
        ->assertOk()
        ->assertJson(['success' => true]);

    $logoPath = setting('cms_logo');
    $firstAbsolutePath = public_path(ltrim((string) $logoPath, '/'));

    try {
        expect($logoPath)->toContain('generated-logos')
            ->and(file_exists($firstAbsolutePath))->toBeTrue();

        $this->actingAs($staff)
            ->post(route('store.generated-logo'), [
                'logo' => UploadedFile::fake()->image('replacement.webp', 120, 50),
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $replacementPath = setting('cms_logo');

        expect($replacementPath)->not->toBe($logoPath)
            ->and(file_exists($firstAbsolutePath))->toBeFalse()
            ->and(file_exists(public_path(ltrim((string) $replacementPath, '/'))))->toBeTrue()
            ->and(File::files(public_path('assets/images/generated-logos')))->toHaveCount(1);
    } finally {
        File::deleteDirectory(public_path('assets/images/generated-logos'));
    }
});

test('users without the permission cannot replace the logo', function () {
    $user = User::factory()->create(['rank' => 1]);

    $this->actingAs($user)
        ->post(route('store.generated-logo'), [
            'logo' => UploadedFile::fake()->image('logo.png', 100, 40),
        ])
        ->assertForbidden();

    expect(setting('cms_logo'))->not->toContain('generated-logos');
});
