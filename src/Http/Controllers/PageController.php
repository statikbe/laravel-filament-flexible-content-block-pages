<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\Conversions\ConversionCollection;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class PageController extends Controller
{
    use ValidatesRequests;

    const CACHE_SEO_IMAGE_DIMENSIONS = 'seo_image_dimensions:%s';
    const CACHE_SEO_IMAGE_TTL = 60 * 60 * 8; // in seconds
    const TEMPLATE_PATH = 'filament-flexible-content-block-pages::pages.';

    // TODO make an abstract model with the table name to use as class to resolve in the route param
    public function index(Page $page)
    {
        // check if page is published:
        if (! Auth::user() || ! Auth::user()->can('viewUnpublishedPages')) {
            if (! $page->isPublished()) {
                SEOMeta::setRobots('noindex');
                abort(Response::HTTP_GONE);
            }
        }

        // SEO
        $this->setBasicSEO($page);
        $this->setSEOLocalisationAndCanonicalUrl();
        $this->setSEOImage($page);

        return view(self::TEMPLATE_PATH.'index', [
            'page' => $page,
        ]);
    }

    protected function getSEOTitlePostfix()
    {
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

    protected function setBasicSEO(Page $page)
    {
        SEOTools::setTitle(($page->seo_title ? $page->seo_title : $page->title).$this->getSEOTitlePostfix());
        SEOTools::setDescription(($page->seo_description ? $page->seo_description : strip_tags($page->intro)));
        SEOTools::opengraph()->setUrl(url()->current());
    }

    protected function setSEOImage(Page $page)
    {
        //TODO copy getSEOMedia in HasSEOAttributesTrait
        $seoMedia = $page->getFallbackImageMedia($page->SEOImage()->first(), $page->getSEOImageCollection());
        $seoUrl = null;
        $imageDimensions = null;

        /*if ($seoMedia) {
            $seoUrl = $seoMedia->getUrl($page->getSEOImageConversionName());
            $imageDimensions = $this->getSEOImageDimensions($seoMedia, $page->getSEOImageConversionName());
        } else if()
            $seoUrl = $seoMedia->getUrl($page->getSEOImageConversionName());
        $imageDimensions = $this->getSEOImageDimensions($seoMedia, $page->getSEOImageConversionName());
            $this->setSEODefaultImage($page);
        }

        if($seoUrl && $imageDimensions) {
            SEOTools::opengraph()->addImage($seoUrl, $imageDimensions);
            SEOTools::twitter()->addValue('image', $seoUrl);
        }*/
    }

    protected function setSEODefaultImage(Page $page)
    {
        $defaultSeoImage = flexiblePagesSettingImageUrl(Settings::COLLECTION_DEFAULT_SEO, Settings::CONVERSION_DEFAULT_SEO);
        // $test = Storage::get($defaultSeoImage);
        if ($defaultSeoImage && trim($defaultSeoImage) != '') {
            $seoParams = $this->getSEOImageDimensions($defaultSeoImage, true);
            $this->setSEOImage($defaultSeoImage, $seoParams);
        } else {
            $imageUrl = $page->getHeroImageUrl($page->getSEOImageConversionName());
            $seoParams = $this->getSEOImageDimensions($imageUrl, true);
            $this->setSEOImage($imageUrl, $seoParams);
        }
    }

    /**
     * Get the dimensions of an image with the given path. Uses cache so the image file does not need to be read each time.
     *
     * @return array|mixed
     */
    protected function getSEOImageDimensions(Media $seoMedia, string $conversion)
    {
        $cacheKey = sprintf(self::CACHE_SEO_IMAGE_DIMENSIONS, $seoMedia->uuid);

        return Cache::remember($cacheKey,
            self::CACHE_SEO_IMAGE_TTL,
            function() use ($seoMedia, $conversion) {
                $conversionCollection = ConversionCollection::createForMedia($seoMedia);
                $conversion = $conversionCollection->getByName($conversion);

                return [
                    'width' => $conversion->getWidth(),
                    'height' => $conversion->getHeight(),
                ];
            });
    }
}
