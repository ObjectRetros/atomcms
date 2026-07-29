<?php

use App\Emulator\EmulatorManager;

/**
 * Every driver ships its own migration directory on top of Atom's shared one.
 * A migration that declares a named class collides the moment two files carry
 * the same name, and PHP cannot redeclare it - so all of them stay anonymous.
 */
test('no migration declares a named class', function () {
    $paths = [database_path('migrations')];

    foreach (array_keys(config('emulator.drivers', [])) as $key) {
        $paths = [...$paths, ...app(EmulatorManager::class)->driver($key)->migrationPaths()];
    }

    $files = collect($paths)
        ->flatMap(fn (string $path) => glob($path . '/*.php') ?: [])
        ->unique();

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $source = (string) file_get_contents($file);

        expect(preg_match('/^\s*(final\s+)?class\s+\w+\s+extends\s+Migration/m', $source))
            ->toBe(0, sprintf('%s declares a named migration class; use "return new class extends Migration".', basename($file)));
    }
});

test('driver migration filenames do not collide across drivers', function () {
    $manager = app(EmulatorManager::class);
    $seen = [];

    foreach (array_keys(config('emulator.drivers', [])) as $key) {
        foreach ($manager->driver($key)->migrationPaths() as $path) {
            foreach (glob($path . '/*.php') ?: [] as $file) {
                $name = basename($file);

                expect(isset($seen[$name]))
                    ->toBeFalse(sprintf(
                        'Migration [%s] is shipped by both [%s] and [%s]',
                        $name, $seen[$name] ?? '?', $key,
                    ));

                $seen[$name] = $key;
            }
        }
    }

    expect($seen)->not->toBeEmpty();
});
