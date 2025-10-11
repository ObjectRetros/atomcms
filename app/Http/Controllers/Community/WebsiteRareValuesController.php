<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\RareSearchFormRequest;
use App\Models\Community\RareValue\WebsiteRareValue;
use App\Models\Community\RareValue\WebsiteRareValueCategory;
use App\Models\Game\Furniture\Item;
use App\Services\Community\RareValues\RareValueCategoriesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class WebsiteRareValuesController extends Controller
{
    public function __construct(private readonly RareValueCategoriesService $valueCategoriesService) {}

    public function index(): View
    {
        return view('rare-values', [
            'categories' => $this->valueCategoriesService->fetchCategoriesByPriority(),
            'categoriesNav' => $this->valueCategoriesService->fetchAllCategories(),
        ]);
    }

    public function category(int $id): View|RedirectResponse
    {
        $category = $this->valueCategoriesService->fetchCategoryById($id);

        if (! $category) {
            return redirect()->back()->withErrors([
                'message' => __('The entered category does not exist'),
            ]);
        }

        return view('rare-values', [
            'categories' => $category,
            'categoriesNav' => $this->valueCategoriesService->fetchAllCategories(),
        ]);
    }

    public function search(RareSearchFormRequest $request): View|RedirectResponse
    {
        $searchTerm = $request->input('search');

        $categories = $this->valueCategoriesService->searchCategories($searchTerm);

        if ($categories->isEmpty()) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like there were no rares matching your search input'),
            ]);
        }

        return view('rare-values', [
            'categories' => $categories,
            'categoriesNav' => WebsiteRareValueCategory::has('furniture')->get(),
        ]);
    }

    public function value(WebsiteRareValue $value): View
    {
        $items = Item::with(['user:id,username,look'])
            ->where('item_id', $value->item_id)
            ->get();

        $itemsPerUser = $items->groupBy('user_id')->map(function ($group) {
            return [
                'user' => $group->first()->user,
                'item_count' => $group->count(),
            ];
        });

        if ((bool) setting('enable_caching')) {
            Cache::remember('allItems_' . $value->id, setting('cache_timer'), function () use ($items) {
                return $items;
            });
        }

        return view('value', [
            'value' => $value,
            'items' => $itemsPerUser,
        ]);
    }
}
