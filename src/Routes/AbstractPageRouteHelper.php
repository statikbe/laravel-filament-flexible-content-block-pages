<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes;

use Illuminate\Support\Facades\Route;
use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\PageController;
use Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts\HandlesPageRoutes;

abstract class AbstractPageRouteHelper implements HandlesPageRoutes
{
    const ROUTE_PAGE = 'filament-flexible-content-block-pages::child_page_index';

    const ROUTE_HOME = 'home';

    const ROUTE_CHILD_PAGE = 'filament-flexible-content-block-pages::child_page_index';

    const ROUTE_GRANDCHILD_PAGE = 'filament-flexible-content-block-pages::grandchild_page_index';

    public function defineRoutes(): void
    {
        Route::get('{grandparent}/{parent}/{page}', [PageController::class, 'grandchildIndex'])
            ->name(static::ROUTE_GRANDCHILD_PAGE);
        Route::get('{parent}/{page}', [PageController::class, 'childIndex'])
            ->name(static::ROUTE_CHILD_PAGE);
        Route::get('{page}', [PageController::class, 'index'])
            ->name(static::ROUTE_PAGE);
        Route::get('/', [PageController::class, 'homeIndex'])
            ->name(static::ROUTE_HOME);
    }
}
