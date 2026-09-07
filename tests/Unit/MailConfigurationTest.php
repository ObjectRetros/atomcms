<?php

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

it('configures the SMTP transport with the requested encryption', function (?string $scheme, ?string $legacyEncryption, int $port, bool $encrypted) {
    $originalEnvironment = $_ENV;
    $originalServer = $_SERVER;
    $originalContainer = Container::getInstance();

    try {
        foreach ([
            'MAIL_SCHEME' => $scheme ?? '(null)',
            'MAIL_ENCRYPTION' => $legacyEncryption ?? '(null)',
            'MAIL_HOST' => 'smtp.example.test',
            'MAIL_PORT' => (string) $port,
            'MAIL_USERNAME' => '(null)',
            'MAIL_PASSWORD' => '(null)',
        ] as $key => $value) {
            $_ENV[$key] = $_SERVER[$key] = $value;
        }

        $app = new Application(dirname(__DIR__, 2));
        $config = require $app->configPath('mail.php');
        $transport = (new MailManager($app))->createSymfonyTransport($config['mailers']['smtp']);

        expect($transport)->toBeInstanceOf(EsmtpTransport::class)
            ->and($transport->isAutoTls())->toBeTrue()
            ->and($transport->getStream())->toBeInstanceOf(SocketStream::class)
            ->and($transport->getStream()->isTLS())->toBe($encrypted)
            ->and($transport->getStream()->getPort())->toBe($port);
    } finally {
        $_ENV = $originalEnvironment;
        $_SERVER = $originalServer;
        Container::setInstance($originalContainer);
    }
})->with([
    'explicit SMTPS on a custom port' => ['smtps', null, 2525, true],
    'legacy SSL on a custom port' => [null, 'ssl', 2525, true],
    'empty scheme preserves legacy SSL' => ['', 'ssl', 2525, true],
    'explicit scheme overrides legacy encryption' => ['smtp', 'ssl', 587, false],
    'implicit TLS on the standard port' => [null, null, 465, true],
    'SMTP with automatic STARTTLS' => ['smtp', null, 587, false],
    'legacy STARTTLS remains supported' => [null, 'tls', 587, false],
    'local Mailpit without implicit TLS' => [null, null, 1025, false],
]);
