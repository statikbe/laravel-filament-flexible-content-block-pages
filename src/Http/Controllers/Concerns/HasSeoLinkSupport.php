<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\Concerns;

use Artesaos\SEOTools\Facades\SEOTools;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

trait HasSeoLinkSupport
{
    /**
     * Adds the canonical and localised URLs.
     */
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
