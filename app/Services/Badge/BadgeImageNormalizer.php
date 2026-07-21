<?php

namespace App\Services\Badge;

use RuntimeException;

class BadgeImageNormalizer
{
    private const MAX_BYTES = 262_144;

    private const SIZE = 40;

    public function toGif(string $bytes): string
    {
        if ($bytes === '' || strlen($bytes) > self::MAX_BYTES) {
            throw new RuntimeException('The badge image is empty or too large.');
        }

        $info = getimagesizefromstring($bytes);

        if (
            $info === false
            || ! in_array($info['mime'], ['image/gif', 'image/png', 'image/jpeg'], true)
            || $info[0] !== self::SIZE
            || $info[1] !== self::SIZE
        ) {
            throw new RuntimeException('Badge images must be 40x40 GIF, PNG, or JPEG files.');
        }

        $image = imagecreatefromstring($bytes);

        if ($image === false) {
            throw new RuntimeException('The badge image could not be decoded.');
        }

        $bufferLevel = ob_get_level();
        $gif = null;
        ob_start();

        try {
            if (! imagegif($image)) {
                throw new RuntimeException('The badge image could not be encoded.');
            }

            $gif = ob_get_clean();
        } finally {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }

            imagedestroy($image);
        }

        if (! is_string($gif) || $gif === '') {
            throw new RuntimeException('The badge image could not be encoded.');
        }

        return $gif;
    }
}
