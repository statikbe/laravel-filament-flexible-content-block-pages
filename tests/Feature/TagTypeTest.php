<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Models\TagType;

it('can create a tag type', function () {
    $tagType = TagType::factory()->create([
        'code' => 'category',
        'name' => ['en' => 'Category', 'es' => 'Categoria'],
        'colour' => '#FF0000',
    ]);

    expect($tagType)->toBeInstanceOf(TagType::class)
        ->and($tagType->code)->toBe('category')
        ->and($tagType->getTranslation('name', 'en'))->toBe('Category');
});

it('can be marked as default type', function () {
    $defaultType = TagType::factory()->default()->create();
    $regularType = TagType::factory()->create();

    expect($defaultType->is_default_type)->toBeTrue()
        ->and($regularType->is_default_type)->toBeFalse();
});

it('resets other default types when setting new default', function () {
    $firstDefault = TagType::factory()->create([
        'code' => 'first',
        'is_default_type' => true,
    ]);

    $secondDefault = TagType::factory()->create([
        'code' => 'second',
        'is_default_type' => true,
    ]);

    $firstDefault->refresh();

    expect($firstDefault->is_default_type)->toBeFalse()
        ->and($secondDefault->is_default_type)->toBeTrue();
});

it('can have SEO pages enabled', function () {
    $seoType = TagType::factory()->seo()->create();
    $regularType = TagType::factory()->create();

    expect($seoType->has_seo_pages)->toBeTrue()
        ->and($regularType->has_seo_pages)->toBeFalse();
});

it('formats colour correctly', function () {
    $hexWithHash = TagType::factory()->withColour('#FF0000')->create();
    $hexWithoutHash = TagType::factory()->withColour('00FF00')->create();
    $rgbColour = TagType::factory()->withColour('rgb(0,0,255)')->create();
    $noColour = TagType::factory()->create(['colour' => null]);

    expect($hexWithHash->formatColour())->toBe('#FF0000')
        ->and($hexWithoutHash->formatColour())->toBe('#00FF00')
        ->and($rgbColour->formatColour())->toBe('rgb(0,0,255)')
        ->and($noColour->formatColour())->toBeNull();
});

it('detects SVG icons', function () {
    $svgIcon = TagType::factory()->withIcon('<svg><path d="M0 0"></path></svg>')->create();
    $textIcon = TagType::factory()->withIcon('heroicon-o-tag')->create();
    $noIcon = TagType::factory()->create(['icon' => null]);

    expect($svgIcon->hasSvgIcon())->toBeTrue()
        ->and($textIcon->hasSvgIcon())->toBeFalse()
        ->and($noIcon->hasSvgIcon())->toBeFalse();
});

it('has many tags relationship', function () {
    $tagType = TagType::factory()->create();
    Tag::factory()->forTagType($tagType)->count(3)->create();

    expect($tagType->tags)->toHaveCount(3);
});

it('returns correct morph class', function () {
    $tagType = TagType::factory()->create();

    expect($tagType->getMorphClass())->toBe('filament-flexible-content-block-pages::tag_type');
});
