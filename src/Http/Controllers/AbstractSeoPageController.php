<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

abstract class AbstractSeoPageController extends Controller
{
    const CACHE_SEO_IMAGE_DIMENSIONS = 'seo_image_dimensions:%s';

    const CACHE_SEO_IMAGE_TTL = 60 * 60 * 8; // in seconds

    protected function setSEOImage(Page $page)
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

    protected function getSettingsTitle(): string
    {
        return flexiblePagesSetting(Settings::SETTING_SITE_TITLE, app()->getLocale(), config('app.name'));
    }

    /**
     * Get the dimensions of an image with the given path. Uses cache so the image file does not need to be read each time.
     */
    protected function getSEOImageDimensions(Media $seoMedia, string $conversion): array
    {
        $cacheKey = sprintf(static::CACHE_SEO_IMAGE_DIMENSIONS, $seoMedia->uuid);

        try {
            return Cache::remember($cacheKey,
                static::CACHE_SEO_IMAGE_TTL,
                function () use ($seoMedia, $conversion) {
                    $filePath = $seoMedia->getPath($conversion);
                    $image = Image::load($filePath);

                    return [
                        'width' => $image->getWidth(),
                        'height' => $image->getHeight(),
                    ];
                });
        } catch (CouldNotLoadImage $exception) { // @phpstan-ignore-line
            return [];
        }
    }

    protected function setBasicSEO(Page $page)
    {
        $title = $this->getValidTitle($page->seo_title) ?? $this->getValidTitle($page->title) ?? $this->getSettingsTitle();
        SEOTools::setTitle($title.$this->getSEOTitlePostfix($page), false);
        SEOTools::setDescription(($page->seo_description ?? strip_tags($page->intro)));
        SEOTools::opengraph()->setUrl(url()->current());
    }

    protected function getValidTitle(?string $title): ?string
    {
        if (! $title) {
            return null;
        }

        if (empty(trim($title))) {
            return null;
        }

        return trim($title);
    }

    protected function getSEOTitlePostfix(Page $page): string
    {
        if ($page->isHomePage()) {
            return '';
        }

        return sprintf(' | %s', flexiblePagesSetting(Settings::SETTING_SITE_TITLE));
    }

    protected function setSEOLocalisationAndCanonicalUrl(): void
    {
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $currentLocale = LaravelLocalization::getCurrentLocale();

        SEOTools::opengraph()->addProperty('locale', $currentLocale);

        unset($supportedLocales[$currentLocale]);
        $otherLocales = array_keys($supportedLocales);
        SEOTools::opengraph()->addProperty('locale:alternate', reset($otherLocales));

        // alternate language urls:
        $translatedUrls = $this->getLocalisedUrls();

        foreach ($translatedUrls as $locale => $url) {
            SEOTools::metatags()->addAlternateLanguage($locale, $url);
        }

        SEOTools::setCanonical($translatedUrls[FilamentFlexibleContentBlockPages::config()->getSEODefaultCanonicalLocale()]);
    }

    /**
     * Returns an array of translated URLS with locale as key.
     *
     * @return array<string, string>
     */
    protected function getLocalisedUrls(): array
    {
        $urls = [];
        foreach (LaravelLocalization::getSupportedLocales() as $locale => $name) {
            if (request()->route()) {
                $urls[$locale] = LaravelLocalization::getLocalizedUrl($locale, url()->full(), request()->route()->parameters());
            }
        }

        return $urls;
    }
}
