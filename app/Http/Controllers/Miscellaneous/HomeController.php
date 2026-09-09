<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Services\Articles\ArticleService;
use App\Services\Community\CameraService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('index', [
            'articles' => app(ArticleService::class)->latestWithAuthor(4),
            'photos' => app(CameraService::class)->latestPhotos(),
        ]);
    }
}
