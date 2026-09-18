<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
use Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures\CustomSettings;

it('retrieves settings singleton', function () {
    $settings = Settings::factory()->create();

    $retrieved = Settings::getSettings();

    expect($retrieved)->toBeInstanceOf(Settings::class)
        ->and($retrieved->id)->toBe($settings->id);
});

it('returns null when no settings exist', function () {
    $settings = Settings::getSettings();

    expect($settings)->toBeNull();
});

it('retrieves setting values', function () {
    Settings::factory()->create([
        'site_title' => 'My Website',
    ]);

    Cache::flush(); // Clear cache to ensure fresh retrieval

    $siteTitle = Settings::setting(Settings::SETTING_SITE_TITLE);

    expect($siteTitle)->toBe('My Website');
});

it('returns translated settings for current locale', function () {
    Settings::factory()->create([
        'contact_info' => [
            'en' => '<p>123 Main St</p>',
            'es' => '<p>Calle Principal 123</p>',
        ],
    ]);

    Cache::flush();

    app()->setLocale('en');
    $englishInfo = Settings::setting(Settings::SETTING_CONTACT_INFO);

    app()->setLocale('es');
    $spanishInfo = Settings::setting(Settings::SETTING_CONTACT_INFO, 'es');

    expect($englishInfo)->toContain('123 Main St')
        ->and($spanishInfo)->toContain('Calle Principal 123');
});

it('returns the requested locale instead of the current locale', function () {
    Settings::factory()->create([
        'contact_info' => [
            'en' => '<p>123 Main St</p>',
            'es' => '<p>Calle Principal 123</p>',
        ],
    ]);

    Cache::flush();
    app()->setLocale('en');

    expect(Settings::setting(Settings::SETTING_CONTACT_INFO, 'es'))->toContain('Calle Principal 123');
});

it('caches a setting per requested locale', function () {
    Settings::factory()->create([
        'contact_info' => [
            'en' => '<p>123 Main St</p>',
            'es' => '<p>Calle Principal 123</p>',
        ],
    ]);

    Cache::flush();
    app()->setLocale('en');

    // warm the cache for both locales while the app locale stays 'en':
    $spanish = Settings::setting(Settings::SETTING_CONTACT_INFO, 'es');
    $english = Settings::setting(Settings::SETTING_CONTACT_INFO, 'en');

    expect($spanish)->toContain('Calle Principal 123')
        ->and($english)->toContain('123 Main St');
});

it('returns a non translatable array setting as is', function () {
    CustomSettings::create([
        'site_title' => 'Test Site',
        'social_links' => ['en' => 'https://example.com/en', 'facebook' => 'https://facebook.com/statik'],
    ]);

    Cache::flush();
    app()->setLocale('en');

    expect(CustomSettings::setting(CustomSettings::SETTING_SOCIAL_LINKS))
        ->toBe(['en' => 'https://example.com/en', 'facebook' => 'https://facebook.com/statik']);
});

it('returns a non string setting with its cast type', function () {
    CustomSettings::create([
        'site_title' => 'Test Site',
        'items_per_page' => 12,
    ]);

    Cache::flush();

    expect(CustomSettings::setting(CustomSettings::SETTING_ITEMS_PER_PAGE))->toBe(12);
});

it('returns a translatable setting added by an extending model', function () {
    $settings = new CustomSettings(['site_title' => 'Test Site']);
    $settings->setTranslation(CustomSettings::SETTING_INTRO, 'en', 'Welcome');
    $settings->setTranslation(CustomSettings::SETTING_INTRO, 'es', 'Bienvenido');
    $settings->save();

    Cache::flush();
    app()->setLocale('en');

    expect(CustomSettings::setting(CustomSettings::SETTING_INTRO))->toBe('Welcome')
        ->and(CustomSettings::setting(CustomSettings::SETTING_INTRO, 'es'))->toBe('Bienvenido');
});

it('keeps the parent media collections when a model registers extra collections', function () {
    $settings = new CustomSettings(['site_title' => 'Test Site']);

    $collectionNames = collect($settings->getRegisteredMediaCollections())->pluck('name')->toArray();

    expect($collectionNames)->toContain(CustomSettings::COLLECTION_DEFAULT_SEO)
        ->and($collectionNames)->toContain(CustomSettings::COLLECTION_LOGO);
});

it('flushes the settings cache when settings media changes', function () {
    $settings = Settings::factory()->create(['site_title' => 'Cached Title']);

    // warm the cache:
    Settings::setting(Settings::SETTING_SITE_TITLE);
    DB::table('fcbp_settings')->update(['site_title' => 'Updated Title']);
    expect(Settings::setting(Settings::SETTING_SITE_TITLE))->toBe('Cached Title');

    // a media upload does not make the model dirty, so it must flush the cache by itself:
    $media = new Media([
        'collection_name' => Settings::COLLECTION_DEFAULT_SEO,
        'name' => 'seo',
        'file_name' => 'seo.jpg',
        'disk' => 'public',
        'size' => 1000,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
    ]);
    $media->model_type = $settings->getMorphClass();
    $media->model_id = $settings->id;
    $media->save();

    expect(Settings::setting(Settings::SETTING_SITE_TITLE))->toBe('Updated Title');
});

it('flushes the settings cache when settings media is deleted', function () {
    $settings = Settings::factory()->create(['site_title' => 'Cached Title']);

    $media = new Media([
        'collection_name' => Settings::COLLECTION_DEFAULT_SEO,
        'name' => 'seo',
        'file_name' => 'seo.jpg',
        'disk' => 'public',
        'size' => 1000,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
    ]);
    $media->model_type = $settings->getMorphClass();
    $media->model_id = $settings->id;
    $media->save();

    // warm the cache:
    Settings::setting(Settings::SETTING_SITE_TITLE);
    DB::table('fcbp_settings')->update(['site_title' => 'Updated Title']);
    expect(Settings::setting(Settings::SETTING_SITE_TITLE))->toBe('Cached Title');

    $media->delete();

    expect(Settings::setting(Settings::SETTING_SITE_TITLE))->toBe('Updated Title');
});

it('caches setting values', function () {
    Settings::factory()->create(['site_title' => 'Cached Title']);

    // First call caches
    $first = Settings::setting(Settings::SETTING_SITE_TITLE);

    // Update directly in DB (bypassing model)
    DB::table('fcbp_settings')->update(['site_title' => 'Updated Title']);

    // Second call should return cached value
    $second = Settings::setting(Settings::SETTING_SITE_TITLE);

    expect($first)->toBe('Cached Title')
        ->and($second)->toBe('Cached Title');
});

it('creates settings with factory', function () {
    $settings = Settings::factory()->create([
        'site_title' => 'Test Site',
        'contact_info' => ['en' => '<p>Contact us</p>', 'es' => '<p>Contactenos</p>'],
        'footer_copyright' => ['en' => '2024 All rights', 'es' => '2024 Todos los derechos'],
    ]);

    expect($settings->site_title)->toBe('Test Site')
        ->and($settings->getTranslation('contact_info', 'en'))->toContain('Contact us')
        ->and($settings->getTranslation('footer_copyright', 'es'))->toContain('2024 Todos los derechos');
});

it('returns correct morph class', function () {
    $settings = Settings::factory()->create();

    expect($settings->getMorphClass())->toBe('filament-flexible-content-block-pages::settings');
});

it('has media collections defined', function () {
    $settings = Settings::factory()->create();

    // Check that media collection is registered
    $collections = $settings->getRegisteredMediaCollections();
    $collectionNames = collect($collections)->pluck('name')->toArray();

    expect($collectionNames)->toContain(Settings::COLLECTION_DEFAULT_SEO);
});
