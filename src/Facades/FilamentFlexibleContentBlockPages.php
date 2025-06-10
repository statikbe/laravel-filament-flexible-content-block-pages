<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPages
 */
class FilamentFlexibleContentBlockPages extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPages::class;
    }
}
