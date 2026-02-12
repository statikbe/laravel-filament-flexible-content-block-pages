<?php

use Illuminate\Support\Facades\Cache;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

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

it('caches setting values', function () {
    Settings::factory()->create(['site_title' => 'Cached Title']);

    // First call caches
    $first = Settings::setting(Settings::SETTING_SITE_TITLE);

    // Update directly in DB (bypassing model)
    \Illuminate\Support\Facades\DB::table('fcbp_settings')->update(['site_title' => 'Updated Title']);

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
