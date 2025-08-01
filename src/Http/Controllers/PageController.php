<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Image\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class PageController extends Controller
{
    use ValidatesRequests;

    const CACHE_SEO_IMAGE_DIMENSIONS = 'seo_image_dimensions:%s';

    const CACHE_SEO_IMAGE_TTL = 60 * 60 * 8; // in seconds

    const TEMPLATE_PATH = 'filament-flexible-content-block-pages::%s.pages.index';

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

        return view($this->getTemplatePath($page), [
            'page' => $page,
        ]);
    }

    public function homeIndex()
    {
        $page = Page::code(Page::HOME_PAGE)
            ->firstOrFail();

        return $this->index($page);
    }

    public function childIndex(Page $parent, Page $page)
    {
        // check if the page is a child of the parent
        if (! $parent->isParentOf($page)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        // render the page with the regular page index function of the controller, or invoke the correct controller here:
        return $this->index($page);
    }

    public function grandchildIndex(Page $grandparent, Page $parent, Page $page)
    {
        // check if the page is a child of the parent
        if (! $parent->isParentOf($page) || ! $grandparent->isParentOf($parent)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        // render the page with the regular page index function of the controller, or invoke the correct controller here:
        return $this->index($page);
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

    protected function setBasicSEO(Page $page)
    {
        $title = $this->getValidTitle($page->seo_title) ?? $this->getValidTitle($page->title) ?? $this->getSettingsTitle();
        SEOTools::setTitle($title.$this->getSEOTitlePostfix($page), false);
        SEOTools::setDescription(($page->seo_description ?? strip_tags($page->intro)));
        SEOTools::opengraph()->setUrl(url()->current());
    }

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

        if ($seoUrl && $imageDimensions) {
            SEOTools::opengraph()->addImage($seoUrl, $imageDimensions);
            SEOTools::twitter()->addValue('image', $seoUrl);
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
            function () use ($seoMedia, $conversion) {
                $filePath = $seoMedia->getPath($conversion);
                $image = Image::load($filePath);

                return [
                    'width' => $image->getWidth(),
                    'height' => $image->getHeight(),
                ];
            });
    }

    private function getSettingsTitle(): string
    {
        return flexiblePagesSetting(Settings::SETTING_SITE_TITLE, app()->getLocale(), config('app.name'));
    }

    private function getValidTitle(?string $title): ?string
    {
        if (! $title) {
            return null;
        }

        if (empty(trim($title))) {
            return null;
        }

        return trim($title);
    }

    private function getTemplatePath(Page $page)
    {
        //handle custom templates
        if($page->code){
            $customTemplate = FilamentFlexibleContentBlockPages::config()->getCustomPageTemplate($page->code);
            if($customTemplate){
                return $customTemplate;
            }
        }

        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        return sprintf(self::TEMPLATE_PATH, $theme);
    }
}
