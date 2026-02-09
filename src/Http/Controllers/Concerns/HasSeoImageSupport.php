<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\Concerns;

use Artesaos\SEOTools\Facades\SEOTools;
use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasHeroImageAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasMediaAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasSEOAttributes;

trait HasSeoImageSupport
{
    /**
     * Returns the cache key to store the dimensions of the SEO image.
     */
    abstract protected function getSEOImageDimensionsCacheKey(Media $media): string;

    /**
     * Returns the cache time to live period to store the dimensions of the SEO image.
     */
    abstract protected function getSEOImageDimensionsCacheTTL(Media $media): Closure|DateInterval|DateTimeInterface|int|null;

    protected function setSEOImage(HasSEOAttributes&HasHeroImageAttributes&HasMediaAttributes $page)
    {
        /** @var Media|null $firstSeoMedia */
        $firstSeoMedia = $page->seoImage()->first();
        $seoMedia = $page->getFallbackImageMedia($firstSeoMedia, $page->getSEOImageCollection());
        $seoUrl = null;
        $imageDimensions = null;

        // 1. try SEO image of the page
        if ($seoMedia) {
            $seoUrl = $seoMedia->getUrl($page->getSEOImageConversionName());
            $imageDimensions = $this->getSEOImageDimensions($seoMedia, $page->getSEOImageConversionName());
        } else {
            // 2. try the hero image of the page
            /** @var Media|null $firstHeroMedia */
            $firstHeroMedia = $page->heroImage()->first();
            $seoMedia = $page->getFallbackImageMedia($firstHeroMedia, $page->getHeroImageCollection());
            if ($seoMedia) {
                $seoUrl = $seoMedia->getUrl($page->getSEOImageConversionName());
                $imageDimensions = $this->getSEOImageDimensions($seoMedia, $page->getSEOImageConversionName());
            }

            // 3. try the default SEO image in the settings
            if (! $seoMedia || ! $seoUrl) {
                /** @var Settings $settings */
                $settings = FilamentFlexibleContentBlockPages::config()->getSettingsModel()::getSettings();

                /** @var Media|null $firstSettingsMedia */
                $firstSettingsMedia = $settings->defaultSeoImage()->first();
                $seoMedia = $settings->getFallbackImageMedia($firstSettingsMedia, $settings::COLLECTION_DEFAULT_SEO);
                if ($seoMedia) {
                    $seoUrl = $seoMedia->getUrl($settings::CONVERSION_DEFAULT_SEO);
                    $imageDimensions = $this->getSEOImageDimensions($seoMedia, $settings::CONVERSION_DEFAULT_SEO);
                }
            }
        }

        if ($seoUrl && ! empty($imageDimensions)) {
            SEOTools::opengraph()->addImage($seoUrl, $imageDimensions);
            SEOTools::twitter()->addValue('image', $seoUrl);
        }
    }

    /**
     * Get the dimensions of an image with the given path. Uses cache so the image file does not need to be read each time.
     */
    protected function getSEOImageDimensions(Media $seoMedia, string $conversion): array
    {
        $cacheKey = $this->getSEOImageDimensionsCacheKey($seoMedia);

        try {
            return Cache::remember($cacheKey,
                $this->getSEOImageDimensionsCacheTTL($seoMedia),
                function () use ($seoMedia, $conversion) {
                    $filePath = $seoMedia->getPath($conversion);
                    $image = Image::load($filePath);

                    return [
                        'width' => $image->getWidth(),
                        'height' => $image->getHeight(),
                    ];
                });
        } catch (CouldNotLoadImage $exception) {
            return [];
        }
    }
}
