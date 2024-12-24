<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Models\Articles\WebsiteArticle;
use App\Models\Miscellaneous\CameraWeb;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('index', [
            'articles' => WebsiteArticle::latest('id')
                ->take(4)
                ->has('user')
                ->with('user:id,username,look')
                ->get(),
            'photos' => CameraWeb::latest('id')
                ->take(4)
                ->where('visible', true)
                ->with('user:id,username,look')
                ->get(),
        ]);
    }
}
