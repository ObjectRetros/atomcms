<?php

namespace App\Http\Controllers;

use App\Http\Requests\RareSearchFormRequest;
use App\Models\WebsiteRareValue;
use App\Models\WebsiteRareValueCategory;
use Illuminate\Http\Request;

class WebsiteRareValuesController extends Controller
{
    public function index()
    {
        return view('rare-values', [
            'categories' => WebsiteRareValueCategory::with('furniture')->get(),
            'categoriesNav' => WebsiteRareValueCategory::all(),
        ]);
    }

    public function category(int $id)
    {
        if (WebsiteRareValueCategory::where('id', '=', $id)->doesntExist()) {
            return redirect()->back()->withErrors([
                'message' => __('The entered category does not exist'),
            ]);
        }

        return view('rare-values', [
            'categories' => WebsiteRareValueCategory::whereId($id)->with('furniture')->get(),
            'categoriesNav' => WebsiteRareValueCategory::all(),
        ]);
    }

    public function search(RareSearchFormRequest $request)
    {
        if (WebsiteRareValue::where('name', 'like', '%' . $request->input('search') . '%')->doesntExist()) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like there were no rares matching your search input'),
            ]);
        }

        // TODO: Exclude categoriees that have no furniture inside of them

        return view('rare-values', [
            'categories' => WebsiteRareValueCategory::with(['furniture' => function($query) use ($request
            ) {
                $query->where('name', 'like', '%' . $request->input('search') . '%');
            }])->get(),
            'categoriesNav' => WebsiteRareValueCategory::all(),
        ]);
    }
}