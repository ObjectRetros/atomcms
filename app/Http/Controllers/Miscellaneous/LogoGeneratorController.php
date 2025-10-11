<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $request->validate(['logo' => 'required|image']);
        $file = $request->file('logo');
        $filename = $file->getClientOriginalName();
        $path = '/assets/images/generated-logos';

        $file->move(public_path($path), $filename);

        $setting = WebsiteSetting::where('key', 'cms_logo')->first();

        $setting->update([
            'value' => sprintf('%s/%s', $path, $filename),
        ]);

        return response()->json(['success' => true, 'message' => 'Logo updated!']);

    }
}
