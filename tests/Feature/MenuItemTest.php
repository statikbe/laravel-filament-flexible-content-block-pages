<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

it('can create menu item with URL type', function () {
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->url('https://example.com')->create();

    expect($menuItem)->toBeInstanceOf(MenuItem::class)
        ->and($menuItem->link_type)->toBe(MenuItem::LINK_TYPE_URL)
        ->and($menuItem->getTranslation('url', 'en'))->toBe('https://example.com');
});

it('can create menu item with route type', function () {
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->route('test.route')->create();

    expect($menuItem->link_type)->toBe(MenuItem::LINK_TYPE_ROUTE)
        ->and($menuItem->route)->toBe('test.route');
});

it('can link to a page model', function () {
    $page = Page::factory()->create();
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->linkedTo($page)->create();

    expect($menuItem->linkable)->toBeInstanceOf(Page::class)
        ->and($menuItem->linkable->id)->toBe($page->id);
});

it('returns URL from linkable model when linked', function () {
    $page = Page::factory()->create([
        'slug' => ['en' => 'about-us', 'es' => 'sobre-nosotros'],
    ]);
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->linkedTo($page)->create();

    $url = $menuItem->getUrl('en');

    expect($url)->toContain('about-us');
});

it('returns custom URL when not linked to model', function () {
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->url('https://external.com')->create();

    expect($menuItem->getUrl('en'))->toBe('https://external.com');
});

it('returns display label from model when use_model_title is true', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'About Us', 'es' => 'Sobre Nosotros'],
    ]);
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()
        ->forMenu($menu)
        ->linkedTo($page)
        ->useModelTitle()
        ->create();

    expect($menuItem->getDisplayLabel('en'))->toBe('About Us');
});

it('returns custom label when use_model_title is false', function () {
    $page = Page::factory()->create();
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()
        ->forMenu($menu)
        ->linkedTo($page)
        ->create([
            'label' => ['en' => 'Custom Label', 'es' => 'Etiqueta Personalizada'],
            'use_model_title' => false,
        ]);

    expect($menuItem->getDisplayLabel('en'))->toBe('Custom Label');
});

it('generates complete URL for route type', function () {
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->route('test.page')->create();

    $url = $menuItem->getCompleteUrl();

    expect($url)->toBe(route('test.page'));
});

it('returns hash for invalid route', function () {
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->create([
        'link_type' => MenuItem::LINK_TYPE_ROUTE,
        'route' => 'non.existent.route',
    ]);

    expect($menuItem->getCompleteUrl())->toBe('#');
});

it('identifies current menu item correctly', function () {
    // Test the static urlsMatch method directly without making HTTP requests
    expect(MenuItem::urlsMatch('http://localhost/current-page', 'http://localhost/current-page'))->toBeTrue()
        ->and(MenuItem::urlsMatch('http://localhost/other-page', 'http://localhost/current-page'))->toBeFalse()
        ->and(MenuItem::urlsMatch('http://localhost/page/', 'http://localhost/page'))->toBeTrue() // trailing slash handling
        ->and(MenuItem::urlsMatch('#', 'http://localhost/page'))->toBeFalse()
        ->and(MenuItem::urlsMatch('', 'http://localhost/page'))->toBeFalse();
});

it('supports visibility scope', function () {
    $menu = Menu::factory()->create();
    MenuItem::factory()->forMenu($menu)->create(['is_visible' => true]);
    MenuItem::factory()->forMenu($menu)->hidden()->create();

    $visibleItems = MenuItem::visible()->get();

    expect($visibleItems)->toHaveCount(1);
});

it('supports tree structure with parent-child relationships', function () {
    $menu = Menu::factory()->create();
    $parent = MenuItem::factory()->forMenu($menu)->create(['parent_id' => \SolutionForest\FilamentTree\Support\Utils::defaultParentId()]);
    $child = MenuItem::factory()->childOf($parent)->create();

    expect($child->parent_id)->toBe($parent->id)
        ->and($parent->children)->toHaveCount(1)
        ->and($parent->children->first()->id)->toBe($child->id);
});

it('returns correct target', function () {
    $menu = Menu::factory()->create();
    $defaultTarget = MenuItem::factory()->forMenu($menu)->create();
    $blankTarget = MenuItem::factory()->forMenu($menu)->withTarget('_blank')->create();

    expect($defaultTarget->getTarget())->toBe('_self')
        ->and($blankTarget->getTarget())->toBe('_blank');
});

it('returns correct morph class', function () {
    $menu = Menu::factory()->create();
    $menuItem = MenuItem::factory()->forMenu($menu)->create();

    expect($menuItem->getMorphClass())->toBe('filament-flexible-content-block-pages::menu-item');
});
