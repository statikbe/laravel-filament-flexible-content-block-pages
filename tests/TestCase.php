<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleContentBlocksServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Statikbe\\FilamentFlexibleContentBlockPages\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            \Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider::class,
            \Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
            FilamentFlexibleContentBlocksServiceProvider::class,
            FilamentFlexibleContentBlockPagesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        // Configure Laravel Localization for tests
        config()->set('laravellocalization.supportedLocales', [
            'en' => ['name' => 'English', 'script' => 'Latn', 'native' => 'English', 'regional' => 'en_GB'],
            'es' => ['name' => 'Spanish', 'script' => 'Latn', 'native' => 'español', 'regional' => 'es_ES'],
        ]);
        config()->set('laravellocalization.hideDefaultLocaleInURL', true);

        // Table names - must match test migrations and config keys
        config()->set('filament-flexible-content-block-pages.table_names.pages', 'fcbp_pages');
        config()->set('filament-flexible-content-block-pages.table_names.menus', 'fcbp_menus');
        config()->set('filament-flexible-content-block-pages.table_names.menu_items', 'fcbp_menu_items');
        config()->set('filament-flexible-content-block-pages.table_names.redirects', 'fcbp_redirects');
        config()->set('filament-flexible-content-block-pages.table_names.tags', 'fcbp_tags');
        config()->set('filament-flexible-content-block-pages.table_names.tag_types', 'fcbp_tag_types');
        config()->set('filament-flexible-content-block-pages.table_names.taggables', 'fcbp_taggables');
        config()->set('filament-flexible-content-block-pages.table_names.settings', 'fcbp_settings');

        // Menu config
        config()->set('filament-flexible-content-block-pages.menu.styles', ['default', 'horizontal', 'vertical']);
        config()->set('filament-flexible-content-block-pages.menu.max_depth', 2);

        // Sitemap config
        config()->set('filament-flexible-content-block-pages.sitemap.enabled', true);
        config()->set('filament-flexible-content-block-pages.sitemap.include_pages', true);
        config()->set('filament-flexible-content-block-pages.sitemap.default_canonical_locale', 'en');

        // Tag pages config
        config()->set('filament-flexible-content-block-pages.tag_pages.models.enabled', [
            \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
        ]);
        config()->set('filament-flexible-content-block-pages.tag_pages.pagination.item_count', 12);

        // SEO config
        config()->set('filament-flexible-content-block-pages.seo.supported_locales', ['en', 'es']);

        // App URL for routes
        config()->set('app.url', 'http://localhost');

        // Enable home route for URL generation
        config()->set('filament-flexible-content-block-pages.enable_home_route', true);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function defineRoutes($router)
    {
        // Use the package's route helper to define routes
        \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::routes();

        // Additional test routes for MenuItem tests
        $router->get('/test-page', fn () => 'test')->name('test.page');
        $router->get('/test-route', fn () => 'test')->name('test.route');
    }
}
