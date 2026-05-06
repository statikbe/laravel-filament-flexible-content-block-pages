<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
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
            LaravelLocalizationServiceProvider::class,
            MediaLibraryServiceProvider::class,
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
            Page::class,
        ]);
        config()->set('filament-flexible-content-block-pages.tag_pages.pagination.item_count', 12);

        // SEO config
        config()->set('filament-flexible-content-block-pages.seo.supported_locales', ['en', 'es']);

        // App URL for routes
        config()->set('app.url', 'http://localhost');

        // Enable home route for URL generation
        config()->set('filament-flexible-content-block-pages.enable_home_route', true);

        // Register laravel-localization middleware aliases for HTTP tests
        $app['router']->aliasMiddleware('localize', LaravelLocalizationRoutes::class);
        $app['router']->aliasMiddleware('localizationRedirect', LaravelLocalizationRedirectFilter::class);
        $app['router']->aliasMiddleware('localeSessionRedirect', LocaleSessionRedirect::class);
        $app['router']->aliasMiddleware('localeViewPath', LaravelLocalizationViewPath::class);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function defineRoutes($router)
    {
        Route::middleware(SubstituteBindings::class)->group(function () use ($router) {
            // Additional test routes - must be BEFORE package routes (which are catch-all)
            $router->get('/test-page', fn () => 'test')->name('test.page');
            $router->get('/test-route', fn () => 'test')->name('test.route');
        });

        // Use the package's route helper to define routes
        // SubstituteBindings is added explicitly since tests don't use the 'web' middleware group
        Route::middleware(SubstituteBindings::class)->group(function () {
            FilamentFlexibleContentBlockPages::routes();
        });
    }
}
