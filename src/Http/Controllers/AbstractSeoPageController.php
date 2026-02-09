<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers;

use Illuminate\Routing\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\Concerns\HasBasicSeoSupport;
use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\Concerns\HasSeoImageSupport;
use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\Concerns\HasSeoLinkSupport;

abstract class AbstractSeoPageController extends Controller
{
    use HasBasicSeoSupport;
    use HasSeoImageSupport;
    use HasSeoLinkSupport;

    const CACHE_SEO_IMAGE_DIMENSIONS = 'filament-flexible-content-block-pages::seo_image_dimensions:%s';

    const CACHE_SEO_IMAGE_TTL = 60 * 60 * 8; // in seconds

    protected function getSEOImageDimensionsCacheKey(Media $media): string
    {
        return sprintf(static::CACHE_SEO_IMAGE_DIMENSIONS, $media->uuid);
    }

    protected function getSEOImageDimensionsCacheTTL(Media $media): \Closure|\DateInterval|\DateTimeInterface|int|null
    {
        return static::CACHE_SEO_IMAGE_TTL;
    }
}
