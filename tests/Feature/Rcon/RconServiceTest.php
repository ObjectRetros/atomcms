<?php

use App\Services\RconService;

test('rcon follows the arcturus one-command-per-connection protocol', function () {
    $errorCode = 0;
    $errorMessage = '';
    $server = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);

    expect($server)->not->toBeFalse();

    $address = stream_socket_get_name($server, false);
    $port = (int) substr((string) $address, strrpos((string) $address, ':') + 1);
    $script = <<<'PHP'
    $server = fopen('php://fd/3', 'r+');
    $commands = ['first', 'second'];
    $valid = true;

    foreach ($commands as $index => $expectedCommand) {
        $connection = stream_socket_accept($server, 5);

        if ($connection === false) {
            exit(1);
        }

        $request = json_decode((string) stream_get_contents($connection), true);
        $valid = $valid
            && $request === ['key' => $expectedCommand, 'data' => ['sequence' => $index + 1]];

        $response = json_encode([
            'status' => $valid ? 0 : 1,
            'message' => "response-{$index}",
        ], JSON_THROW_ON_ERROR);
        $middle = intdiv(strlen($response), 2);

        fwrite($connection, substr($response, 0, $middle));
        usleep(10_000);
        fwrite($connection, substr($response, $middle));
        fclose($connection);
    }

    fclose($server);
    exit($valid ? 0 : 1);
    PHP;
    $process = proc_open(
        [PHP_BINARY, '-r', $script],
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
            3 => $server,
        ],
        $pipes,
    );

    expect($process)->not->toBeFalse();

    fclose($server);
    fclose($pipes[0]);

    try {
        $rcon = new RconService('127.0.0.1', $port);
        $first = $rcon->sendCommand('first', ['sequence' => 1]);
        $second = $rcon->sendCommand('second', ['sequence' => 2]);
    } finally {
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);
    }

    expect($first->successful())->toBeTrue()
        ->and($first->message)->toBe('response-0')
        ->and($second->successful())->toBeTrue()
        ->and($second->message)->toBe('response-1')
        ->and($exitCode)->toBe(0, $stderr ?: $stdout);
});
