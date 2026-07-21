<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Actions\ReplaceGeneratedLogo;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogoGeneratorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LogoGeneratorController extends Controller
{
    public function index(): RedirectResponse|View
    {
        if (! hasPermission('generate_logo')) {
            return to_route('me.show')->with([
                'message' => __('You do not have permission to do this.'),
            ]);
        }

        return view('logo-generator');
    }

    public function store(LogoGeneratorRequest $request, ReplaceGeneratedLogo $replaceLogo): JsonResponse
    {
        $replaceLogo->execute($request->file('logo'));

        return response()->json(['success' => true, 'message' => 'Logo updated!']);
    }
}
