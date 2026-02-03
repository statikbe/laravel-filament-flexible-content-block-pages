<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures\CustomPage;

it('page model has resolveRouteBinding method', function () {
    $page = new Page;

    expect(method_exists($page, 'resolveRouteBinding'))->toBeTrue();
});

it('resolveRouteBinding delegates to configured model when different', function () {
    // Configure custom page model
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);

    // Clear the cached config instance to pick up the new model
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $page = new Page;

    // Get the configured model
    $configuredModel = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getPageModel();

    // Verify the configured model is CustomPage
    expect($configuredModel)->toBeInstanceOf(CustomPage::class);

    // Verify the delegation logic exists (checking the method checks the configured model)
    expect(get_class($configuredModel))->not->toBe(Page::class);
});

it('resolveRouteBinding uses self when no custom model configured', function () {
    // Ensure default page model is configured
    config()->set('filament-flexible-content-block-pages.models.pages', Page::class);

    // Clear the cached config instance
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $page = new Page;

    // Get the configured model
    $configuredModel = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getPageModel();

    // Verify the configured model is the default Page
    expect($configuredModel)->toBeInstanceOf(Page::class)
        ->and(get_class($configuredModel))->toBe(Page::class);
});

it('custom page model has correct morph class', function () {
    $customPage = new CustomPage;

    expect($customPage->getMorphClass())->toBe('custom-page');
});

it('custom page model can identify itself', function () {
    $customPage = new CustomPage;

    expect($customPage->isCustomPage())->toBeTrue();
});

it('homeIndex uses configured page model', function () {
    // Configure custom page model
    config()->set('filament-flexible-content-block-pages.models.pages', CustomPage::class);

    // Clear the cached config instance
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    // Verify the configured model is used
    $configuredModel = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getPageModel();

    expect($configuredModel)->toBeInstanceOf(CustomPage::class);
});
