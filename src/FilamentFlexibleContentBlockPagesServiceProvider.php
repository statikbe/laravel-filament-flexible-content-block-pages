<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Statik\LaravelLeisure\LaravelLeisure;
use Statikbe\FilamentFlexibleContentBlockPages\Commands\SeedDefaultsCommand;
use Statikbe\FilamentFlexibleContentBlockPages\Components\BaseLayout;
use Statikbe\FilamentFlexibleContentBlockPages\Components\LanguageSwitch;

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
            ])
            ->hasTranslations()
            ->hasCommand(SeedDefaultsCommand::class)
            ->hasViewComponents('flexible-pages',
                LanguageSwitch::class,
                BaseLayout::class,
            );
    }

    public function packageBooted()
    {
        // add morph map
        Relation::morphMap(\Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::config()->getMorphMap());;
    }
}
