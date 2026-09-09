<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Badge\CreateDrawnBadge;
use App\Actions\ReplaceGeneratedLogo;
use App\Data\PublicUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Badge\BadgePurchaseRequest;
use App\Http\Requests\LogoGeneratorRequest;
use App\Http\Resources\Api\V1\PublicUserResource;
use App\Models\Community\RareValue\WebsiteRareValue;
use App\Services\Client\ClientLaunchService;
use App\Services\Community\RareValues\RareValueCategoriesService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function launch(Request $request, ClientLaunchService $client): JsonResponse
    {
        $validated = $request->validate(['client' => ['sometimes', 'in:nitro,flash']]);
        $type = $validated['client'] ?? 'nitro';
        abort_if($type === 'flash' && ! config('habbo.client.flash_enabled'), 404);
        $sso = $client->ticket(AuthenticatedUser::from($request), $request->ip() ?: 'unknown');
        $data = ['client' => $type, 'sso' => $sso];
        if ($type === 'nitro') {
            $data['url'] = rtrim(url((string) setting('nitro_path', config('habbo.client.nitro_path'))), '/') . '/index.html?sso=' . rawurlencode($sso);
        } else {
            $base = rtrim(url((string) config('habbo.flash.swf_base_path')), '/');
            $data['url'] = $base . '/' . config('habbo.flash.production_folder') . '/' . config('habbo.flash.habbo_swf');
            $data['host'] = config('habbo.flash.host');
            $data['port'] = config('habbo.flash.port');
            foreach (['external_productdata', 'external_furnidata', 'external_texts', 'external_variables', 'external_figuredata', 'external_figuremap', 'external_override_texts', 'external_override_variables'] as $key) {
                $data[$key] = $base . '/' . config('habbo.flash.' . $key);
            }
        }

        return response()->json(['data' => $data])->header('Cache-Control', 'no-store, private');
    }

    public function badges(): JsonResponse
    {
        return response()->json(['data' => ['cost' => (int) setting('drawbadge_currency_value', 150), 'currency' => setting('drawbadge_currency_type', 'credits')]]);
    }

    public function buyBadge(BadgePurchaseRequest $request, CreateDrawnBadge $badges): JsonResponse
    {
        $badge = $badges->execute(AuthenticatedUser::from($request), ['badge_data' => $request->string('badge_data')->toString(), 'badge_name' => $request->string('badge_name')->toString(), 'badge_description' => $request->string('badge_description')->toString()]);

        return response()->json(['data' => ['badge_url' => url($badge->badge_url)]], 201);
    }

    public function logo(LogoGeneratorRequest $request, ReplaceGeneratedLogo $logos): JsonResponse
    {
        return response()->json(['data' => ['url' => url($logos->execute($request->file('logo')))]]);
    }

    public function rareValues(Request $request, RareValueCategoriesService $values): JsonResponse
    {
        $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'category' => ['sometimes', 'integer', 'min:1']]);
        $categories = $request->filled('search') ? $values->searchCategories($request->string('search')->toString()) : $values->fetchCategoriesByPriority();
        if ($request->filled('category')) {
            $categories = $categories->where('id', $request->integer('category'));
        }

        return response()->json(['data' => $categories->map(fn ($category): array => ['id' => $category->id, 'name' => $category->name, 'badge' => $category->badge, 'values' => $category->furniture->map(fn ($value): array => $this->valueData($value))])->values()]);
    }

    public function rareValue(WebsiteRareValue $value, RareValueCategoriesService $values): JsonResponse
    {
        return response()->json(['data' => [...$this->valueData($value), 'holdings' => collect($values->itemsPerUser($value))->map(fn ($row): array => ['user' => $row['user'] ? new PublicUserResource(PublicUserData::from($row['user'])) : null, 'count' => $row['item_count']])]]);
    }

    /** @return array<string, mixed> */
    private function valueData(WebsiteRareValue $value): array
    {
        return ['id' => $value->id, 'name' => $value->name, 'icon' => url(rtrim((string) setting('furniture_icons_path'), '/') . '/' . ltrim($value->furniture_icon, '/')), 'item_id' => $value->item_id, 'is_limited' => $value->isLimitedEdition(), 'credit_value' => $value->credit_value, 'currency_value' => $value->currency_value, 'currency_type' => $value->currency_type];
    }
}
