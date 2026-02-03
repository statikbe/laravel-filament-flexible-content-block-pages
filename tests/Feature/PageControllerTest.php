<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures\CustomPage;

it('resolves default page model via route binding', function () {
    $page = Page::factory()->create([
        'slug' => ['en' => 'about-us', 'es' => 'sobre-nosotros'],
    ]);

    $resolvedPage = (new Page)->resolveRouteBinding('about-us');

    expect($resolvedPage)->toBeInstanceOf(Page::class)
        ->and($resolvedPage->id)->toBe($page->id);
});

it('resolves page by translated slug in any locale', function () {
    $page = Page::factory()->create([
        'slug' => ['en' => 'contact', 'es' => 'contacto'],
    ]);

    $resolvedEnglish = (new Page)->resolveRouteBinding('contact');
    $resolvedSpanish = (new Page)->resolveRouteBinding('contacto');

    expect($resolvedEnglish->id)->toBe($page->id)
        ->and($resolvedSpanish->id)->toBe($page->id);
});

it('returns null when page slug not found', function () {
    Page::factory()->create([
        'slug' => ['en' => 'existing-page'],
    ]);

    $resolved = (new Page)->resolveRouteBinding('non-existent-slug');

    expect($resolved)->toBeNull();
});

it('resolves custom page model when configured', function () {
    // Configure custom page model
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);

    // Clear the cached config instance to pick up the new model
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $page = CustomPage::factory()->create([
        'slug' => ['en' => 'custom-page', 'es' => 'pagina-personalizada'],
    ]);

    // When resolving through the package's Page model, it should delegate to CustomPage
    $resolvedPage = (new Page)->resolveRouteBinding('custom-page');

    expect($resolvedPage)->toBeInstanceOf(CustomPage::class)
        ->and($resolvedPage->id)->toBe($page->id)
        ->and($resolvedPage->isCustomPage())->toBeTrue();
});

it('returns correct morph class for custom page model', function () {
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $page = CustomPage::factory()->create([
        'slug' => ['en' => 'test-morph'],
    ]);

    $resolvedPage = (new Page)->resolveRouteBinding('test-morph');

    expect($resolvedPage->getMorphClass())->toBe('custom-page');
});

it('handles parent and child page resolution with custom model', function () {
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $parent = CustomPage::factory()->create([
        'slug' => ['en' => 'parent-page'],
    ]);

    $child = CustomPage::factory()->childOf($parent)->create([
        'slug' => ['en' => 'child-page'],
    ]);

    $resolvedParent = (new Page)->resolveRouteBinding('parent-page');
    $resolvedChild = (new Page)->resolveRouteBinding('child-page');

    expect($resolvedParent)->toBeInstanceOf(CustomPage::class)
        ->and($resolvedChild)->toBeInstanceOf(CustomPage::class)
        ->and($resolvedParent->isParentOf($resolvedChild))->toBeTrue();
});

it('does not resolve when using default model but custom is configured', function () {
    // Create a page with the default model
    $defaultPage = Page::factory()->create([
        'slug' => ['en' => 'default-model-page'],
    ]);

    // Now configure custom model
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    // The page created with default model should still be found
    // but returned as CustomPage instance
    $resolved = (new Page)->resolveRouteBinding('default-model-page');

    // It finds the page but returns it as CustomPage
    expect($resolved)->toBeInstanceOf(CustomPage::class)
        ->and($resolved->id)->toBe($defaultPage->id);
});

it('homeIndex finds home page with default model', function () {
    $homePage = Page::factory()->homePage()->create();

    $foundPage = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()
        ->getPageModel()::code(Page::HOME_PAGE)
        ->first();

    expect($foundPage)->toBeInstanceOf(Page::class)
        ->and($foundPage->id)->toBe($homePage->id)
        ->and($foundPage->isHomePage())->toBeTrue();
});

it('homeIndex finds home page with custom model configured', function () {
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $homePage = CustomPage::factory()->homePage()->create();

    $foundPage = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()
        ->getPageModel()::code(Page::HOME_PAGE)
        ->first();

    expect($foundPage)->toBeInstanceOf(CustomPage::class)
        ->and($foundPage->id)->toBe($homePage->id)
        ->and($foundPage->isHomePage())->toBeTrue()
        ->and($foundPage->isCustomPage())->toBeTrue();
});

it('homeIndex returns custom model with correct morph class', function () {
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    CustomPage::factory()->homePage()->create();

    $foundPage = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()
        ->getPageModel()::code(Page::HOME_PAGE)
        ->first();

    expect($foundPage->getMorphClass())->toBe('custom-page');
});
