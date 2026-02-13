<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes;

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;

/**
 * Handles localised URLs.
 */
class LocalisedPageRouteHelper extends AbstractPageRouteHelper
{
    public function definePageRoutes(): void
    {
        Route::group([
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'localize'],
        ], function () {
            parent::definePageRoutes();
        });
    }

    public function defineSeoTagRoutes(): void
    {
        Route::group([
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'localize'],
        ], function () {
            parent::defineSeoTagRoutes();
        });
    }

    public function getUrl(Page $page, ?string $locale = null): string
    {
        if ($page->isHomePage()) {
            return LaravelLocalization::getLocalizedUrl($locale, static::ROUTE_HOME);
        }

        if ($page->hasParent()) {
            $page->loadMissing('parent.parent');
        }

        $ancestorSlugs = [];
        $locale = $locale ?? app()->getLocale();

        if (! $page->isRoot() && ! $page->parent?->isRoot() && $page->parent?->parent) { /** @phpstan-ignore-line */
            /** @var Page $grandparent */
            $grandparent = $page->parent->parent;
            $ancestorSlugs[] = $grandparent->translate('slug', $locale);
        }

        if (! $page->isRoot() && $page->parent) {
            /** @var Page $parent */
            $parent = $page->parent;
            $ancestorSlugs[] = $parent->translate('slug', $locale);
        }

        $ancestorSlugs[] = $page->translate('slug', $locale);

        return LaravelLocalization::getLocalizedUrl($locale, route(static::ROUTE_HOME)).'/'.implode('/', $ancestorSlugs);
    }

    public function getTagPageUrl(Tag $tag, ?string $locale = null): string
    {
        return LaravelLocalization::getLocalizedUrl($locale, route(static::ROUTE_SEO_TAG_PAGE, ['tag' => $tag]));
    }
}
