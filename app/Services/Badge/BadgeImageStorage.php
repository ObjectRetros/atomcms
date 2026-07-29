<?php

namespace App\Services\Badge;

use App\Services\Files\AtomicFileWriter;
use App\Support\BadgeCode;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final readonly class BadgeImageStorage
{
    public function __construct(
        private readonly RemoteBadgeImageFetcher $remoteImages,
        private readonly BadgeImageNormalizer $normalizer,
        private readonly AtomicFileWriter $files,
    ) {}

    public function storeRemote(string $code, string $url): void
    {
        $this->store($code, $this->remoteImages->fetch($url));
    }

    public function store(string $code, string $bytes): void
    {
        $path = Storage::disk('badges')->path(BadgeCode::filename($code));
        File::ensureDirectoryExists(dirname($path));

        $this->files->replace($path, $this->normalizer->toGif($bytes));
    }
}
