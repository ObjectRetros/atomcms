<?php

namespace App\Support;

use Closure;
use RuntimeException;
use Throwable;

final class AtomicFileWriter
{
    /** @param Closure(string): string $mutate */
    public function rewrite(string $path, Closure $mutate): void
    {
        $path = realpath($path) ?: $path;

        if (! is_file($path) || ! is_readable($path) || ! is_writable($path)) {
            throw new RuntimeException("File cannot be safely rewritten: {$path}");
        }

        $directory = dirname($path);

        if (! is_writable($directory)) {
            throw new RuntimeException("File directory is not writable: {$directory}");
        }

        $lock = fopen($path . '.lock', 'c');

        if ($lock === false) {
            throw new RuntimeException("Unable to open file lock: {$path}.lock");
        }

        try {
            if (! flock($lock, LOCK_EX)) {
                throw new RuntimeException("Unable to acquire file lock: {$path}.lock");
            }

            $contents = file_get_contents($path);

            if ($contents === false) {
                throw new RuntimeException("Unable to read file: {$path}");
            }

            $this->replace($path, $mutate($contents));
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function replace(string $path, string $contents): void
    {
        $mode = fileperms($path);

        if ($mode === false) {
            throw new RuntimeException("Unable to read file permissions: {$path}");
        }

        $temporaryPath = tempnam(dirname($path), '.' . basename($path) . '.');

        if ($temporaryPath === false) {
            throw new RuntimeException("Unable to create temporary file for: {$path}");
        }

        try {
            $this->write($temporaryPath, $contents);

            if (! chmod($temporaryPath, $mode & 0777)) {
                throw new RuntimeException("Unable to preserve file permissions: {$path}");
            }

            if (! rename($temporaryPath, $path)) {
                throw new RuntimeException("Unable to replace file: {$path}");
            }
        } catch (Throwable $exception) {
            if (is_file($temporaryPath)) {
                try {
                    unlink($temporaryPath);
                } catch (Throwable $cleanupException) {
                    throw new RuntimeException(
                        "Unable to clean up temporary file: {$temporaryPath}",
                        previous: $exception,
                    );
                }
            }

            throw $exception;
        }
    }

    private function write(string $path, string $contents): void
    {
        $file = fopen($path, 'wb');

        if ($file === false) {
            throw new RuntimeException("Unable to open temporary file: {$path}");
        }

        try {
            $remaining = $contents;

            while ($remaining !== '') {
                $written = fwrite($file, $remaining);

                if ($written === false || $written === 0) {
                    throw new RuntimeException("Unable to write temporary file: {$path}");
                }

                $remaining = substr($remaining, $written);
            }

            if (! fflush($file) || ! fsync($file)) {
                throw new RuntimeException("Unable to flush temporary file: {$path}");
            }
        } finally {
            fclose($file);
        }
    }
}
