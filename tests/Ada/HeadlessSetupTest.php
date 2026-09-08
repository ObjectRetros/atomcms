<?php

use App\Emulator\Contracts\RankRepository;
use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

it('creates the explicitly named headless administrator through Ada identity rules', function () {
    WebsiteInstallation::query()->delete();
    Cache::flush();
    $previousEmail = getenv('ATOM_ADMIN_EMAIL');
    $previousPassword = getenv('ATOM_ADMIN_PASSWORD');
    putenv('ATOM_ADMIN_EMAIL=ada-operator@example.test');
    putenv('ATOM_ADMIN_PASSWORD=Example-password-42!');
    try {
        $this->artisan('atom:setup', ['--complete' => true, '--admin' => 'AdaOperator', '--no-interaction' => true])
            ->assertSuccessful();
        $admin = User::where('username', 'AdaOperator')->sole();
        expect($admin->rank)->toBe(app(RankRepository::class)->highestRank());
        expect(Hash::check('Example-password-42!', $admin->password))->toBeTrue();
        expect(WebsiteInstallation::where('completed', true)->exists())->toBeTrue();
    } finally {
        putenv($previousEmail === false ? 'ATOM_ADMIN_EMAIL' : 'ATOM_ADMIN_EMAIL=' . $previousEmail);
        putenv($previousPassword === false ? 'ATOM_ADMIN_PASSWORD' : 'ATOM_ADMIN_PASSWORD=' . $previousPassword);
    }
});
