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

it('getUrl returns cached URL by code', function () {
    $page = Page::factory()->withCode('ABOUT')->create([
        'slug' => ['en' => 'about-us', 'es' => 'sobre-nosotros'],
    ]);

    $url = Page::getUrl('ABOUT', 'en');

    expect($url)->toContain('about-us');
});

it('getUrl returns null for non-existent code', function () {
    $url = Page::getUrl('NON_EXISTENT', 'en');

    expect($url)->toBeNull();
});

it('getUrl returns cached result on subsequent calls', function () {
    Page::factory()->withCode('CACHED')->create([
        'slug' => ['en' => 'cached-page'],
    ]);

    $url1 = Page::getUrl('CACHED', 'en');

    // Delete the page from the database directly (bypass observer)
    Page::query()->where('code', 'CACHED')->delete();

    // Should still return the cached URL
    $url2 = Page::getUrl('CACHED', 'en');

    expect($url1)->toBe($url2);
});

it('getUrl returns different URLs per locale', function () {
    Page::factory()->withCode('LOCALIZED')->create([
        'slug' => ['en' => 'english-page', 'es' => 'pagina-espanol'],
    ]);

    $enUrl = Page::getUrl('LOCALIZED', 'en');
    $esUrl = Page::getUrl('LOCALIZED', 'es');

    expect($enUrl)->toContain('english-page')
        ->and($esUrl)->toContain('pagina-espanol');
});

it('getByCode returns cached page model', function () {
    $page = Page::factory()->withCode('CONTACT')->create([
        'title' => ['en' => 'Contact Us'],
    ]);

    $found = Page::getByCode('CONTACT');

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($page->id)
        ->and($found->title)->toBe('Contact Us');
});

it('getByCode returns null for non-existent code', function () {
    $found = Page::getByCode('NON_EXISTENT');

    expect($found)->toBeNull();
});

it('getByCode returns cached result on subsequent calls', function () {
    $page = Page::factory()->withCode('CACHED_MODEL')->create();

    $found1 = Page::getByCode('CACHED_MODEL');

    // Delete the page directly (bypass observer)
    Page::query()->where('code', 'CACHED_MODEL')->delete();

    // Should still return the cached model
    $found2 = Page::getByCode('CACHED_MODEL');

    expect($found1->id)->toBe($found2->id);
});

it('clearCache invalidates getUrl cache', function () {
    $page = Page::factory()->withCode('CLEAR_URL')->create([
        'slug' => ['en' => 'original-slug'],
    ]);

    // Prime the cache
    $originalUrl = Page::getUrl('CLEAR_URL', 'en');
    expect($originalUrl)->toContain('original-slug');

    // Update slug directly in DB (bypassing observer to control cache clearing manually)
    Page::query()->where('id', $page->id)->update(['slug' => json_encode(['en' => 'updated-slug'])]);

    // Cache should still return old URL
    $cachedUrl = Page::getUrl('CLEAR_URL', 'en');
    expect($cachedUrl)->toContain('original-slug');

    // Clear cache manually
    $page->clearCache();

    // Should now fetch fresh URL
    $newUrl = Page::getUrl('CLEAR_URL', 'en');
    expect($newUrl)->toContain('updated-slug');
});

it('clearCache invalidates getByCode cache', function () {
    $page = Page::factory()->withCode('CLEAR_MODEL')->create([
        'title' => ['en' => 'Original Title'],
    ]);

    // Prime the cache
    $original = Page::getByCode('CLEAR_MODEL');
    expect($original->title)->toBe('Original Title');

    // Clear cache and update
    $page->clearCache();
    $page->update(['title' => ['en' => 'Updated Title']]);

    // Should fetch fresh model
    $updated = Page::getByCode('CLEAR_MODEL');
    expect($updated->title)->toBe('Updated Title');
});

it('observer clears cache on page update', function () {
    $page = Page::factory()->withCode('OBSERVER_UPDATE')->create([
        'title' => ['en' => 'Before Update'],
    ]);

    // Prime the cache
    Page::getByCode('OBSERVER_UPDATE');

    // Update via model (triggers observer)
    $page->update(['title' => ['en' => 'After Update']]);

    // Cache should be invalidated by observer
    $found = Page::getByCode('OBSERVER_UPDATE');
    expect($found->title)->toBe('After Update');
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
