<?php

namespace App\Services\Community;

use App\Data\PublicUserData;
use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Models\Miscellaneous\CameraWeb;
use Illuminate\Database\Eloquent\Collection;

class CameraService
{
    public function fetchPhotos(bool $paginate = false, int $perPage = 8): mixed
    {
        $photos = CameraWeb::where('visible', true)
            ->latest('id')
            ->with('user:' . implode(',', PublicUserData::COLUMNS));

        return $paginate ? $photos->paginate($perPage) : $photos->get();
    }

    /** @return Collection<int, CameraWeb> */
    public function latestPhotos(int $limit = 4): Collection
    {
        if (! Emulator::supports(Feature::CameraPhotos)) {
            return new Collection;
        }

        return CameraWeb::latest('id')->take($limit)->where('visible', true)->with('user:' . implode(',', PublicUserData::COLUMNS))->get();
    }
}
