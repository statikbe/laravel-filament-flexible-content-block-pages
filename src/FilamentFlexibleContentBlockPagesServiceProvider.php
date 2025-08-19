<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\MissingPageRedirector\Redirector\Redirector;
use Statikbe\FilamentFlexibleContentBlockPages\Commands\GenerateSitemapCommand;
use Statikbe\FilamentFlexibleContentBlockPages\Commands\SeedDefaultsCommand;
use Statikbe\FilamentFlexibleContentBlockPages\Components\BaseLayout;
use Statikbe\FilamentFlexibleContentBlockPages\Components\LanguageSwitch;
use Statikbe\FilamentFlexibleContentBlockPages\Components\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Components\MenuItem;
use Statikbe\FilamentFlexibleContentBlockPages\Listeners\SlugChangedListener;
use Statikbe\FilamentFlexibleContentBlockPages\Services\Contracts\GeneratesSitemap;
use Statikbe\FilamentFlexibleContentBlocks\Events\SlugChanged;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleContentBlocks;

class FilamentFlexibleContentBlockPagesServiceProvider extends PackageServiceProvider
{
    const PACKAGE_PREFIX = 'filament-flexible-content-block-pages';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-'.self::PACKAGE_PREFIX)
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_filament_flexible_content_block_pages_table',
                'create_filament_flexible_content_block_pages_redirects_table',
                'create_filament_flexible_content_block_pages_settings_table',
                'create_filament_flexible_content_block_tags_table',
                'create_filament_flexible_content_block_menus_table',
                'create_filament_flexible_content_block_menu_items_table',
            ])
            ->hasTranslations()
            ->hasCommands([
                SeedDefaultsCommand::class,
                GenerateSitemapCommand::class,
            ])
            ->hasViewComponents('flexible-pages',
                LanguageSwitch::class,
                BaseLayout::class,
                Menu::class,
                MenuItem::class,
            );
    }

    public function packageBooted()
    {
        // add morph map
        Relation::morphMap(\Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getMorphMap());

        $configName = $this->package->shortName();
        $this->mergeConfigFrom(__DIR__.'/../config/'.$configName.'.php', $configName);

        FilamentFlexibleContentBlocks::setLocales(LaravelLocalization::getSupportedLanguagesKeys());

        // Override spatie/laravel-missing-page-redirector's redirector - this runs after all packages are registered
        $this->app->bind(Redirector::class, config('filament-flexible-content-block-pages.redirects.redirector'));
    }

    public function packageRegistered()
    {
        // Bind sitemap generator interface to the configured implementation
        $this->app->bind(
            GeneratesSitemap::class,
            config('filament-flexible-content-block-pages.sitemap.generator_service', \Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService::class)
        );

        // register slug changed listener
        Event::listen(SlugChanged::class, SlugChangedListener::class);
    }
}
