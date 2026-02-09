<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Models\TagType;
use Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures\CustomTag;

it('resolves default tag model via route binding', function () {
    $tagType = TagType::factory()->create();

    // Create tag with specific name - Spatie's HasSlug trait auto-generates slug from name
    $tag = Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'Technology', 'es' => 'Tecnologia'],
    ]);

    // Slug is auto-generated as 'technology'
    $resolvedTag = (new Tag)->resolveRouteBinding('technology');

    expect($resolvedTag)->toBeInstanceOf(Tag::class)
        ->and($resolvedTag->id)->toBe($tag->id);
});

it('resolves tag by translated slug in any locale', function () {
    $tagType = TagType::factory()->create();

    $tag = Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'News', 'es' => 'Noticias'],
    ]);

    // Slugs are auto-generated from names
    $resolvedEnglish = (new Tag)->resolveRouteBinding('news');
    $resolvedSpanish = (new Tag)->resolveRouteBinding('noticias');

    expect($resolvedEnglish->id)->toBe($tag->id)
        ->and($resolvedSpanish->id)->toBe($tag->id);
});

it('returns null when tag slug not found', function () {
    $tagType = TagType::factory()->create();

    Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'Existing Tag'],
    ]);

    $resolved = (new Tag)->resolveRouteBinding('non-existent-slug');

    expect($resolved)->toBeNull();
});

it('resolves custom tag model when configured', function () {
    $tagType = TagType::factory()->create();

    // Configure custom tag model
    config()->set('filament-flexible-content-block-pages.models.tags', CustomTag::class);

    // Clear the cached config instance to pick up the new model
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $tag = CustomTag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'Custom Tag', 'es' => 'Etiqueta Personalizada'],
    ]);

    // When resolving through the package's Tag model, it should delegate to CustomTag
    // Slug is auto-generated as 'custom-tag'
    $resolvedTag = (new Tag)->resolveRouteBinding('custom-tag');

    expect($resolvedTag)->toBeInstanceOf(CustomTag::class)
        ->and($resolvedTag->id)->toBe($tag->id)
        ->and($resolvedTag->isCustomTag())->toBeTrue();
});

it('returns correct morph class for custom tag model', function () {
    $tagType = TagType::factory()->create();

    config()->set('filament-flexible-content-block-pages.models.tags', CustomTag::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $tag = CustomTag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'Test Morph Tag'],
    ]);

    // Slug is auto-generated as 'test-morph-tag'
    $resolvedTag = (new Tag)->resolveRouteBinding('test-morph-tag');

    expect($resolvedTag->getMorphClass())->toBe('custom-tag');
});

it('resolves tag with tag type relationship intact', function () {
    $tagType = TagType::factory()->create([
        'code' => 'category',
        'name' => ['en' => 'Category'],
    ]);

    $tag = Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'My Category'],
    ]);

    // Slug is auto-generated as 'my-category'
    $resolvedTag = (new Tag)->resolveRouteBinding('my-category');

    expect($resolvedTag)->toBeInstanceOf(Tag::class)
        ->and($resolvedTag->tagType)->toBeInstanceOf(TagType::class)
        ->and($resolvedTag->tagType->code)->toBe('category');
});

it('resolves custom tag with tag type relationship intact', function () {
    config()->set('filament-flexible-content-block-pages.models.tags', CustomTag::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    $tagType = TagType::factory()->create([
        'code' => 'topic',
        'name' => ['en' => 'Topic'],
    ]);

    $tag = CustomTag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'Custom Topic'],
    ]);

    // Slug is auto-generated as 'custom-topic'
    $resolvedTag = (new Tag)->resolveRouteBinding('custom-topic');

    expect($resolvedTag)->toBeInstanceOf(CustomTag::class)
        ->and($resolvedTag->tagType)->toBeInstanceOf(TagType::class)
        ->and($resolvedTag->tagType->code)->toBe('topic')
        ->and($resolvedTag->isCustomTag())->toBeTrue();
});

it('does not affect other tags when custom model is configured', function () {
    $tagType = TagType::factory()->create();

    // Create a tag with the default model
    $defaultTag = Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'Default Tag'],
    ]);

    // Now configure custom model
    config()->set('filament-flexible-content-block-pages.models.tags', CustomTag::class);
    app()->forgetInstance(\Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig::class);

    // The tag created with default model should still be found
    // but returned as CustomTag instance
    // Slug is auto-generated as 'default-tag'
    $resolved = (new Tag)->resolveRouteBinding('default-tag');

    // It finds the tag but returns it as CustomTag
    expect($resolved)->toBeInstanceOf(CustomTag::class)
        ->and($resolved->id)->toBe($defaultTag->id);
});
