<?php

// Separate processes reproduce grants whose surrounding transactions overlap.
// Clone the real badge table shapes so DDL and committed writes never touch
// the test suite's player data or its RefreshDatabase transaction.

use App\Emulator\Drivers\Ada\AdaBadgeRepository;
use App\Emulator\Drivers\Arcturus\ArcturusBadgeRepository;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$driver = $argv[1];
$differentPlayers = ($argv[4] ?? 'same') === 'different';
$mode = $argv[2] ?? 'coordinator';
$directory = $argv[3] ?? sys_get_temp_dir() . '/atom-badge-race-' . uniqid();
$prefix = 'badge_race_' . substr(hash('sha256', $directory), 0, 12) . '_';
$tables = $driver === 'ada' ? ['badges', 'player_badges', 'website_badge_grant_locks'] : ['users_badges', 'website_badge_grant_locks'];
$connection = DB::connection();

if ($mode === 'coordinator') {
    mkdir($directory, 0700);
    foreach ($tables as $table) {
        $connection->statement("DROP TABLE IF EXISTS `{$prefix}{$table}`");
        $connection->statement("CREATE TABLE `{$prefix}{$table}` LIKE `{$table}`");
    }
    try {
        $first = new Process([PHP_BINARY, __FILE__, $driver, 'first', $directory, $differentPlayers ? 'different' : 'same'], timeout: 10);
        $second = new Process([PHP_BINARY, __FILE__, $driver, 'second', $directory, $differentPlayers ? 'different' : 'same'], timeout: 10);
        $first->start();
        $second->start();
        $first->wait();
        $second->wait();
        if (! $first->isSuccessful() || ! $second->isSuccessful() || trim($first->getOutput()) !== 'completed' || trim($second->getOutput()) !== 'completed') {
            throw new RuntimeException($first->getOutput() . $first->getErrorOutput() . $second->getOutput() . $second->getErrorOutput());
        }
        $ownedTable = $driver === 'ada' ? 'player_badges' : 'users_badges';
        $owned = $connection->table($prefix . $ownedTable)->count();
        $definitions = $driver === 'ada' ? $connection->table($prefix . 'badges')->count() : null;
        echo json_encode(['driver' => $driver, 'ownership_rows' => $owned, 'definition_rows' => $definitions]) . PHP_EOL;
    } finally {
        if (isset($first)) {
            $first->stop(0.1);
        }
        if (isset($second)) {
            $second->stop(0.1);
        }
        foreach ($tables as $table) {
            $connection->statement("DROP TABLE IF EXISTS `{$prefix}{$table}`");
        }
        foreach (glob($directory . '/*') as $file) {
            unlink($file);
        }
        rmdir($directory);
    }
    exit;
}

$connection->setTablePrefix($prefix);
$user = new User;
$user->forceFill(['id' => $differentPlayers && $mode === 'second' ? 9999998 : 9999999]);
$badges = $driver === 'ada' ? new AdaBadgeRepository : new ArcturusBadgeRepository;
$waitFor = function (string $file): void {
    $deadline = microtime(true) + 5;
    while (! is_file($file)) {
        if (microtime(true) > $deadline) {
            throw new RuntimeException('Timed out waiting for competing grant.');
        }
        usleep(10000);
    }
};
if ($mode === 'first') {
    DB::transaction(function () use ($badges, $user, $directory, $waitFor): void {
        $badges->grant($user, 'ACH_Concurrent');
        touch($directory . '/first-granted');
        $waitFor($directory . '/second-started');
        usleep(300000);
    }, attempts: 3);
} else {
    $waitFor($directory . '/first-granted');
    DB::transaction(function () use ($badges, $user, $directory, $driver): void {
        DB::table($driver === 'ada' ? 'player_badges' : 'users_badges')->count();
        touch($directory . '/second-started');
        $badges->grant($user, 'ACH_Concurrent');
    }, attempts: 3);
}

echo 'completed';
