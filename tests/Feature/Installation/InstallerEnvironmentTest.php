<?php

use App\Console\Commands\AtomInstallCommand;
use Dotenv\Dotenv;

test('installer database credentials round trip through dotenv without interpolation', function (string $password, bool $replaceExisting) {
    $contents = "EXISTING_VALUE=expanded\nAPP_DEBUG=false\n";

    if ($replaceExisting) {
        $contents .= "DB_PASSWORD=old-password\n";
    }

    $replace = new ReflectionMethod(AtomInstallCommand::class, 'replaceEnvValue');
    $updated = $replace->invoke(new AtomInstallCommand, $contents, 'DB_PASSWORD', $password, '/test/.env');
    $parsed = Dotenv::parse($updated);

    expect($parsed['DB_PASSWORD'])->toBe($password)
        ->and($parsed['APP_DEBUG'])->toBe('false')
        ->and($parsed['EXISTING_VALUE'])->toBe('expanded')
        ->and($parsed)->toHaveCount(3);
})->with([
    'backreference' => ['password$1value'],
    'dotenv expansion' => ['password${EXISTING_VALUE}'],
    'comment character' => ['password#value'],
    'quotes and spaces' => ['password "quoted" value'],
    'backslashes' => ['password\\value\\'],
    'literal newline escape' => ['password\\nvalue'],
    'line injection' => ["password\nAPP_DEBUG=true"],
    'carriage return' => ["password\rvalue"],
    'empty' => [''],
])->with([true, false]);
