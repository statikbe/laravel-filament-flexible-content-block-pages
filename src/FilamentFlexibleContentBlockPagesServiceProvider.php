<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Illuminate\Database\Eloquent\Relations\Relation;
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
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleContentBlocks;

class FilamentFlexibleContentBlockPagesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-filament-flexible-content-block-pages')
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
    }

    public function packageRegistered()
    {
        $this->app->bind(
            \Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService::class,
            function ($app) {
                $serviceClass = \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getSitemapGeneratorService();

                return $app->make($serviceClass);
            }
        );

        // set our custom redirector for spatie/laravel-missing-page-redirector
        $this->app->bind(Redirector::class, config('filament-flexible-content-block-pages.redirects.redirector'));
    }
}
