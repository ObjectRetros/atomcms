<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogoGeneratorRequest;
use App\Models\Miscellaneous\WebsiteSetting;
use App\Services\SettingsService;
use Illuminate\Support\Str;

class LogoGeneratorController extends Controller
{
    public function index()
    {
        if (! hasPermission('generate_logo')) {
            return to_route('me.show')->with([
                'message' => __('You do not have permission to do this.'),
            ]);
        }

        return view('logo-generator');
    }

    public function store(LogoGeneratorRequest $request)
    {
        $file = $request->file('logo');
        $directory = 'assets/images/generated-logos';
        $filename = Str::uuid() . '.' . ($file->guessExtension() ?: 'png');

        $file->move(public_path($directory), $filename);

        WebsiteSetting::updateOrCreate(['key' => 'cms_logo'], [
            'value' => sprintf('/%s/%s', $directory, $filename),
            'comment' => 'CMS logo path',
        ]);

        SettingsService::clearCache();

        return response()->json(['success' => true, 'message' => 'Logo updated!']);
    }
}
