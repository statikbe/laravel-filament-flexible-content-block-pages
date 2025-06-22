<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
                'create_pages_table',
                'create_redirects_table',
                'create_settings_table',
            ])
            ->hasTranslations();
    }
}
