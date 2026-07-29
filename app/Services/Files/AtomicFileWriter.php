<?php

namespace App\Services\Files;

use RuntimeException;

final readonly class AtomicFileWriter
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
        $directory = dirname($path);

        if (! is_writable($directory)) {
            throw new RuntimeException("Directory is not writable: {$directory}");
        }

        $lockPath = $path . '.lock';
        $lock = $this->acquireLock($lockPath);

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
            $candidatePath = tempnam($directory, '.atom-');

            if ($candidatePath === false) {
                throw new RuntimeException("Unable to create temporary file for: {$path}");
            }

            $temporaryPath = $candidatePath;

            if (file_put_contents($temporaryPath, $updated) !== strlen($updated)) {
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

            $this->releaseLock($lockPath, $lock);
        }
    }

    /**
     * Open and lock the sidecar, retrying when the inode we locked was
     * unlinked by a holder that finished between our open and our lock.
     *
     * @return resource
     */
    private function acquireLock(string $lockPath)
    {
        while (true) {
            $lock = fopen($lockPath, 'c');

            if ($lock === false) {
                throw new RuntimeException("Unable to lock file: {$lockPath}");
            }

            if (! flock($lock, LOCK_EX)) {
                fclose($lock);

                throw new RuntimeException("Unable to lock file: {$lockPath}");
            }

            $opened = fstat($lock);
            clearstatcache(true, $lockPath);
            $onDisk = @stat($lockPath);

            if ($opened !== false && $onDisk !== false && $opened['ino'] === $onDisk['ino']) {
                return $lock;
            }

            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Unlink the sidecar before releasing the lock, so managed files do not
     * leak a permanent .lock companion.
     *
     * @param  resource  $lock
     */
    private function releaseLock(string $lockPath, $lock): void
    {
        @unlink($lockPath);
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}
