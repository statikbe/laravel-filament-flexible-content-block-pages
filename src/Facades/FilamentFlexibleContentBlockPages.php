<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Facades;

use Illuminate\Support\Facades\Facade;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

/**
 * @method static FilamentFlexibleContentBlockPagesConfig config()
 * @method static void routes()
 * @method static string getUrl(Page $page, ?string $locale = null)
 * @method static Settings settings()
 *
 * @see \Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPages
 */
class FilamentFlexibleContentBlockPages extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPages::class;
    }
}
