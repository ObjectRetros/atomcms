<?php

use App\Models\Game\Furniture\CatalogPage;
use App\Services\Catalog\CatalogReorderService;
use App\Services\Catalog\CatalogTreeService;
use Illuminate\Validation\ValidationException;

function hierarchyPage(string $caption, int $parentId = -1, int $order = 10): CatalogPage
{
    return CatalogPage::create([
        'caption' => $caption,
        'caption_save' => $caption,
        'parent_id' => $parentId,
        'order_num' => $order,
    ]);
}

test('a catalog page cannot move beneath itself or a descendant', function (bool $self) {
    $parent = hierarchyPage('Parent');
    $child = hierarchyPage('Child', $parent->id);
    $destination = $self ? $parent : $child;

    expect(fn () => app(CatalogReorderService::class)->movePage($parent->id, $destination->id, 0))
        ->toThrow(ValidationException::class);

    expect($parent->refresh()->parent_id)->toBe(-1)
        ->and($child->refresh()->parent_id)->toBe($parent->id);
})->with([true, false]);

test('a catalog page cannot move into a missing parent', function () {
    $page = hierarchyPage('Page');

    expect(fn () => app(CatalogReorderService::class)->movePage($page->id, 999999999, 0))
        ->toThrow(ValidationException::class);

    expect($page->refresh()->parent_id)->toBe(-1);
});

test('valid catalog moves preserve sibling ordering and root moves', function () {
    $parent = hierarchyPage('Parent');
    $first = hierarchyPage('First', $parent->id);
    $last = hierarchyPage('Last', $parent->id, 20);
    $moved = hierarchyPage('Moved');
    $reorder = app(CatalogReorderService::class);

    $reorder->movePage($moved->id, $parent->id, 1);

    expect(CatalogPage::where('parent_id', $parent->id)->orderBy('order_num')->pluck('id')->all())
        ->toBe([$first->id, $moved->id, $last->id]);

    $reorder->movePage($moved->id, -1, 0);

    expect($moved->refresh()->parent_id)->toBe(-1)
        ->and(app(CatalogTreeService::class)->breadcrumb($last))->toHaveCount(2)
        ->and(app(CatalogTreeService::class)->expandToReveal(collect([$last->id, $first->id])))
        ->toEqualCanonicalizing([$parent->id, $last->id, $first->id]);
});

test('existing catalog cycles cannot trap breadcrumbs or ancestor expansion', function () {
    $first = hierarchyPage('First');
    $second = hierarchyPage('Second', $first->id);
    $first->update(['parent_id' => $second->id]);
    $tree = app(CatalogTreeService::class);

    expect(array_map(fn (CatalogPage $page) => $page->id, $tree->breadcrumb($first)))
        ->toBe([$second->id, $first->id])
        ->and($tree->expandToReveal(collect([$first->id, $second->id])))
        ->toEqualCanonicalizing([$first->id, $second->id]);
});

test('a catalog page cannot join an existing cycle but can be moved out of one', function () {
    $first = hierarchyPage('First');
    $second = hierarchyPage('Second', $first->id);
    $first->update(['parent_id' => $second->id]);
    $page = hierarchyPage('Page');
    $reorder = app(CatalogReorderService::class);

    expect(fn () => $reorder->movePage($page->id, $first->id, 0))
        ->toThrow(ValidationException::class);

    $reorder->movePage($first->id, -1, 0);

    expect($first->refresh()->parent_id)->toBe(-1)
        ->and(app(CatalogTreeService::class)->breadcrumb($second))->toHaveCount(2);
});
