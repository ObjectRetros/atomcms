<?php

namespace App\Support;

use RuntimeException;

final class EnvironmentFile
{
    /** @param array<string, string> $values */
    public function write(string $path, array $values): void
    {
        $contents = file_get_contents($path);
        if (! is_string($contents)) {
            throw new RuntimeException('Unable to read the environment file.');
        }
        foreach ($values as $key => $value) {
            $contents = $this->replace($contents, $key, $value);
        }
        $temporary = tempnam(dirname($path), '.atom-env-');
        if ($temporary === false) {
            throw new RuntimeException('Unable to stage the environment file.');
        }
        try {
            chmod($temporary, fileperms($path) & 0777);
            if (file_put_contents($temporary, $contents, LOCK_EX) === false || ! rename($temporary, $path)) {
                throw new RuntimeException('Unable to replace the environment file.');
            }
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }

    public function replace(string $contents, string $key, string $value): string
    {
        if (! preg_match('/^[A-Z_][A-Z0-9_]*$/', $key)) {
            throw new RuntimeException('Invalid environment key.');
        }
        $escaped = strtr($value, ['\\' => '\\\\', '"' => '\\"', '$' => '\\$', "\r" => '\\r', "\n" => '\\n']);
        $line = sprintf('%s="%s"', $key, $escaped);
        $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

        return preg_match($pattern, $contents) === 1
            ? (preg_replace_callback($pattern, fn (): string => $line, $contents) ?? throw new RuntimeException('Unable to replace environment value.'))
            : rtrim($contents, "\r\n") . PHP_EOL . $line . PHP_EOL;
    }
}
