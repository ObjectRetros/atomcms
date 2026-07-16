<?php

namespace App\Services\Files;

use RuntimeException;

class AtomicFileWriter
{
    public function replace(string $path, string $contents): void
    {
        $this->update($path, static fn (): string => $contents);
    }

    /**
     * @param  callable(string): string  $mutate
     */
    public function update(string $path, callable $mutate): void
    {
        if (! is_writable(dirname($path))) {
            throw new RuntimeException("File is not readable and writable: {$path}");
        }

        $lock = fopen($path . '.lock', 'c');

        if ($lock === false) {
            throw new RuntimeException("Unable to lock file: {$path}");
        }

        if (! flock($lock, LOCK_EX)) {
            fclose($lock);

            throw new RuntimeException("Unable to lock file: {$path}");
        }

        $temporaryPath = null;

        try {
            $exists = file_exists($path);

            if ($exists && (! is_file($path) || ! is_readable($path) || ! is_writable($path))) {
                throw new RuntimeException("File is not readable and writable: {$path}");
            }

            $current = $exists ? file_get_contents($path) : '';

            if ($current === false) {
                throw new RuntimeException("Unable to read file: {$path}");
            }

            $updated = $mutate($current);
            $temporaryPath = tempnam(dirname($path), '.atom-');

            if ($temporaryPath === false || file_put_contents($temporaryPath, $updated) !== strlen($updated)) {
                throw new RuntimeException("Unable to write temporary file for: {$path}");
            }

            $permissions = $exists ? fileperms($path) : 0644;

            if ($permissions === false || ! chmod($temporaryPath, $permissions & 0777)) {
                throw new RuntimeException("Unable to set file permissions for: {$path}");
            }

            if (! rename($temporaryPath, $path)) {
                throw new RuntimeException("Unable to replace file: {$path}");
            }

            $temporaryPath = null;
        } finally {
            if ($temporaryPath !== null && file_exists($temporaryPath)) {
                unlink($temporaryPath);
            }

            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
