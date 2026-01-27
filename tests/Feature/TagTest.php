<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Models\TagType;

it('can create a tag with translations', function () {
    $tagType = TagType::factory()->create();
    $tag = Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'Technology', 'es' => 'Tecnologia'],
        'slug' => ['en' => 'technology', 'es' => 'tecnologia'],
        'seo_description' => ['en' => 'Tech articles', 'es' => 'Articulos de tecnologia'],
    ]);

    expect($tag)->toBeInstanceOf(Tag::class)
        ->and($tag->getTranslation('name', 'en'))->toBe('Technology')
        ->and($tag->getTranslation('name', 'es'))->toBe('Tecnologia')
        ->and($tag->getTranslation('slug', 'en'))->toBe('technology');
});

it('belongs to a tag type', function () {
    $tagType = TagType::factory()->create(['code' => 'category']);
    $tag = Tag::factory()->forTagType($tagType)->create();

    expect($tag->tagType)->toBeInstanceOf(TagType::class)
        ->and($tag->tagType->code)->toBe('category');
});

it('generates view URL for SEO page', function () {
    $tagType = TagType::factory()->withSeoPages()->create();

    // Create tag with specific name - Spatie's HasSlug trait will auto-generate slug from name
    $tag = Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'My Tag', 'es' => 'Mi Etiqueta'],
    ]);

    $url = $tag->getViewUrl('en');

    // Slug is auto-generated from name as 'my-tag'
    expect($url)->toContain('my-tag');
});

it('returns localized route key', function () {
    $tagType = TagType::factory()->create();

    // Create tag with specific names - Spatie's HasSlug trait auto-generates slug from name
    $tag = Tag::factory()->forTagType($tagType)->create([
        'name' => ['en' => 'English Tag', 'es' => 'Etiqueta Espanola'],
    ]);

    // Slugs are auto-generated from names
    expect($tag->getLocalizedRouteKey('en'))->toBe('english-tag')
        ->and($tag->getLocalizedRouteKey('es'))->toBe('etiqueta-espanola');
});

it('uses slug as route key name', function () {
    $tagType = TagType::factory()->create();
    $tag = Tag::factory()->forTagType($tagType)->create();

    expect($tag->getRouteKeyName())->toBe('slug');
});

it('returns correct morph class', function () {
    $tagType = TagType::factory()->create();
    $tag = Tag::factory()->forTagType($tagType)->create();

    expect($tag->getMorphClass())->toBe('filament-flexible-content-block-pages::tag');
});
