<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

it('can create a page', function () {
    $page = Page::factory()->create([
        'title' => ['en' => 'Test Page', 'es' => 'Pagina de Prueba'],
        'slug' => ['en' => 'test-page', 'es' => 'pagina-de-prueba'],
    ]);

    expect($page)->toBeInstanceOf(Page::class)
        ->and($page->title)->toBe('Test Page')
        ->and($page->slug)->toBe('test-page');
});

it('generates correct view URL', function () {
    $page = Page::factory()->create([
        'slug' => ['en' => 'about-us', 'es' => 'sobre-nosotros'],
    ]);

    $url = $page->getViewUrl('en');

    expect($url)->toContain('about-us');
});

it('generates view URL for different locales', function () {
    $page = Page::factory()->create([
        'slug' => ['en' => 'about-us', 'es' => 'sobre-nosotros'],
    ]);

    $englishUrl = $page->getViewUrl('en');
    $spanishUrl = $page->getViewUrl('es');

    expect($englishUrl)->toContain('about-us')
        ->and($spanishUrl)->toContain('sobre-nosotros');
});

it('identifies home page correctly', function () {
    $homePage = Page::factory()->homePage()->create();
    $regularPage = Page::factory()->create();

    expect($homePage->isHomePage())->toBeTrue()
        ->and($regularPage->isHomePage())->toBeFalse();
});

it('prevents deletion of undeletable pages via observer', function () {
    $page = Page::factory()->undeletable()->create();

    $deleted = $page->delete();

    expect($deleted)->toBeFalse()
        ->and(Page::find($page->id))->not->toBeNull();
});

it('prevents deletion of menu-linked pages via observer', function () {
    $page = Page::factory()->create();
    $menu = Menu::factory()->create();
    MenuItem::factory()->forMenu($menu)->linkedTo($page)->create();

    // Refresh the page to load the menuItem relationship
    $page->refresh();

    $deleted = $page->delete();

    expect($deleted)->toBeFalse()
        ->and(Page::find($page->id))->not->toBeNull();
});

it('allows deletion of regular pages', function () {
    $page = Page::factory()->create();

    expect($page->isDeletable())->toBeTrue();

    $deleted = $page->delete();

    expect($deleted)->toBeTrue()
        ->and(Page::find($page->id))->toBeNull();
});

it('supports parent-child relationships', function () {
    $parent = Page::factory()->create();
    $child = Page::factory()->childOf($parent)->create();

    expect($parent->isParentOf($child))->toBeTrue()
        ->and($child->hasParent())->toBeTrue()
        ->and($child->parent_id)->toBe($parent->id);
});

it('retrieves page by code', function () {
    $page = Page::factory()->withCode('ABOUT')->create();

    $foundPage = Page::code('ABOUT')->first();

    expect($foundPage)->not->toBeNull()
        ->and($foundPage->id)->toBe($page->id);
});

it('returns correct morph class', function () {
    $page = Page::factory()->create();

    expect($page->getMorphClass())->toBe('filament-flexible-content-block-pages::page');
});

it('can check if page is published', function () {
    $published = Page::factory()->create([
        'publishing_begins_at' => now()->subDay(),
        'publishing_ends_at' => null,
    ]);
    $unpublished = Page::factory()->unpublished()->create();
    $scheduled = Page::factory()->scheduled()->create();
    $expired = Page::factory()->expired()->create();

    expect($published->isPublished())->toBeTrue()
        ->and($unpublished->isPublished())->toBeFalse()
        ->and($scheduled->isPublished())->toBeFalse()
        ->and($expired->isPublished())->toBeFalse();
});
