<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Statikbe\FilamentFlexibleContentBlockPages\Commands\GenerateSitemapCommand;
use Statikbe\FilamentFlexibleContentBlockPages\Commands\SeedDefaultsCommand;
use Statikbe\FilamentFlexibleContentBlockPages\Components\BaseLayout;
use Statikbe\FilamentFlexibleContentBlockPages\Components\LanguageSwitch;
use Statikbe\FilamentFlexibleContentBlockPages\Components\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Components\MenuItem;

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
    }

    public function packageRegistered()
    {
        $this->app->bind(
            \Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService::class,
            function ($app) {
                $serviceClass = FilamentFlexibleContentBlockPages::config()->getSitemapGeneratorService();

                return $app->make($serviceClass);
            }
        );
    }
}
