<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes;

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

/**
 * Handles localised URLs.
 */
class LocalisedPageRouteHelper extends AbstractPageRouteHelper
{
    public function defineRoutes(): void
    {
        Route::group([
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'localize'],
        ], function () {
            parent::defineRoutes();
        });
    }

    public function getUrl(Page $page, ?string $locale = null): string
    {
        if ($page->isHomePage()) {
            return LaravelLocalization::getLocalizedUrl($locale, static::ROUTE_HOME);
        }

        $ancestorSlugs = [];
        $locale = $locale ?? app()->getLocale();

        if ($page->parent?->parent) {
            /** @var Page $grandparent */
            $grandparent = $page->parent->parent;
            $ancestorSlugs[] = $grandparent->translate('slug', $locale);
        }

        if ($page->parent) {
            /** @var Page $parent */
            $parent = $page->parent;
            $ancestorSlugs[] = $parent->translate('slug', $locale);
        }

        $ancestorSlugs[] = $page->translate('slug', $locale);

        return LaravelLocalization::getLocalizedUrl($locale, route(static::ROUTE_HOME)).'/'.implode('/', $ancestorSlugs);
    }
}
