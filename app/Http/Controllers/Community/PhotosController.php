<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Services\Community\CameraService;
use Illuminate\View\View;

class PhotosController extends Controller
{
    public function __construct(private readonly CameraService $cameraService) {}

    public function __invoke(): View
    {
        return view('community.photos', [
            'photos' => $this->cameraService->fetchPhotos(true),
        ]);
    }
}
