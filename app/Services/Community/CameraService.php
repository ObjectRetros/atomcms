<?php

namespace App\Services\Community;

use App\Models\Miscellaneous\CameraWeb;

class CameraService
{
    public function fetchPhotos(bool $paginate = false, int $perPage = 8): mixed
    {
        $photos = CameraWeb::where('visible', true)
            ->latest('id')
            ->with('user:id,username,look');

        return $paginate ? $photos->paginate($perPage) : $photos->get();
    }
}
