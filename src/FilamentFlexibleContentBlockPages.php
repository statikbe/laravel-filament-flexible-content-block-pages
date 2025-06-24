<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Illuminate\Support\Facades\Route;
use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\PageController;

class FilamentFlexibleContentBlockPages
{
    public function config(): FilamentFlexibleContentBlockPagesConfig
    {
        return app(FilamentFlexibleContentBlockPagesConfig::class);
    }

    public function routes(): void
    {
        Route::get('{grandparent}/{parent}/{page}', [PageController::class, 'grandchildIndex'])
            ->name('filament-flexible-content-block-pages::child_page_index');
        Route::get('{parent}/{page}', [PageController::class, 'childIndex'])
            ->name('filament-flexible-content-block-pages::child_page_index');
        Route::get('{page}', [PageController::class, 'index'])
            ->name('filament-flexible-content-block-pages::page_index');
        Route::get('/', [PageController::class, 'homeIndex'])
            ->name('home');
    }
}
