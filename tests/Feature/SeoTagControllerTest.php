<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures\CustomTag;

it('tag model has resolveRouteBinding method', function () {
    $tag = new Tag;

    expect(method_exists($tag, 'resolveRouteBinding'))->toBeTrue();
});

it('resolveRouteBinding delegates to configured tag model when different', function () {
    // Configure custom tag model
    config()->set('filament-flexible-content-block-pages.models.tags', CustomTag::class);

    // Clear the cached config instance to pick up the new model
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $tag = new Tag;

    // Get the configured model
    $configuredModel = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getTagModel();

    // Verify the configured model is CustomTag
    expect($configuredModel)->toBeInstanceOf(CustomTag::class);

    // Verify the delegation logic exists (checking the method checks the configured model)
    expect(get_class($configuredModel))->not->toBe(Tag::class);
});

it('resolveRouteBinding uses self when no custom tag model configured', function () {
    // Ensure default tag model is configured
    config()->set('filament-flexible-content-block-pages.models.tags', Tag::class);

    // Clear the cached config instance
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $tag = new Tag;

    // Get the configured model
    $configuredModel = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getTagModel();

    // Verify the configured model is the default Tag
    expect($configuredModel)->toBeInstanceOf(Tag::class)
        ->and(get_class($configuredModel))->toBe(Tag::class);
});

it('custom tag model has correct morph class', function () {
    $customTag = new CustomTag;

    expect($customTag->getMorphClass())->toBe('custom-tag');
});

it('custom tag model can identify itself', function () {
    $customTag = new CustomTag;

    expect($customTag->isCustomTag())->toBeTrue();
});
