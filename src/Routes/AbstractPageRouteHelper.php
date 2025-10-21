<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes;

use Illuminate\Support\Facades\Route;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\PageController;
use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\SeoTagController;
use Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts\HandlesPageRoutes;

abstract class AbstractPageRouteHelper implements HandlesPageRoutes
{
    public function defineRoutes(): void
    {
        if (FilamentFlexibleContentBlockPages::config()->areTagPagesEnabled()) {
            $this->defineSeoTagRoutes();
        }

        $this->definePageRoutes();
    }

    public function definePageRoutes(): void
    {
        Route::get('{grandparent}/{parent}/{page}', [PageController::class, 'grandchildIndex'])
            ->name(static::ROUTE_GRANDCHILD_PAGE);
        Route::get('{parent}/{page}', [PageController::class, 'childIndex'])
            ->name(static::ROUTE_CHILD_PAGE);
        Route::get('{page}', [PageController::class, 'index'])
            ->name(static::ROUTE_PAGE);
        if (FilamentFlexibleContentBlockPages::config()->isHomePageRouteEnabled()) {
            Route::get('/', [PageController::class, 'homeIndex'])
                ->name(static::ROUTE_HOME);
        }
    }

    public function defineSeoTagRoutes(): void
    {
        Route::get($this->getTagPageRoute(), [SeoTagController::class, 'index'])
            ->name(static::ROUTE_SEO_TAG_PAGE);
    }

    protected function getTagPageRoute(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getTagPageRoutePrefix().'/{tag:slug}';
    }
}
