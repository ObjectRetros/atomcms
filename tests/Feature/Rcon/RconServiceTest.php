<?php

use App\Services\RconService;

test('rcon follows the arcturus one-command-per-connection protocol', function () {
    $script = <<<'PHP'
    $errorCode = 0;
    $errorMessage = '';
    $server = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);

    if ($server === false) {
        fwrite(STDERR, "Unable to create RCON test server: {$errorMessage} ({$errorCode})");
        exit(1);
    }

    $address = stream_socket_get_name($server, false);
    $separator = is_string($address) ? strrpos($address, ':') : false;
    $port = $separator === false ? 0 : (int) substr($address, $separator + 1);

    if ($port < 1 || $port > 65535) {
        fwrite(STDERR, 'Unable to determine RCON test server port');
        fclose($server);
        exit(1);
    }

    fwrite(STDOUT, $port . PHP_EOL);
    fflush(STDOUT);

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
        ],
        $pipes,
    );

    expect($process)->not->toBeFalse();

    fclose($pipes[0]);

    try {
        $portLine = fgets($pipes[1]);
        $port = filter_var(trim((string) $portLine), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 65535],
        ]);

        expect($portLine)->not->toBeFalse()
            ->and($port)->not->toBeFalse();

        $rcon = new RconService('127.0.0.1', (int) $port);
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
