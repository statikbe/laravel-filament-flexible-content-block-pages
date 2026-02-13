<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\Concerns;

use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasIntroAttribute;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasPageAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasSEOAttributes;

trait HasBasicSeoSupport
{
    protected function getSettingsTitle(): string
    {
        return flexiblePagesSetting(Settings::SETTING_SITE_TITLE, app()->getLocale(), config('app.name'));
    }

    /**
     * @phpstan-param HasSEOAttributes&HasPageAttributes&HasIntroAttribute&object{title: string|null, intro: string|null, seo_keywords: array<string>|null} $page
     */
    protected function setBasicSEO(HasSEOAttributes&HasPageAttributes&HasIntroAttribute $page): void
    {
        $title = $this->getValidText($page->getSEOTitle()) ?? $this->getValidText($page->title) ?? $this->getSettingsTitle();
        SEOTools::setTitle($title.$this->getSEOTitlePostfix(), false);
        SEOTools::setDescription(($this->getValidText($page->getSEODescription()) ?? strip_tags($page->intro)));
        SEOTools::opengraph()->setUrl(url()->current());

        if ($page->seo_keywords) {
            SEOMeta::setKeywords($page->seo_keywords);
        }
    }

    protected function getValidText(?string $title): ?string
    {
        if (! $title) {
            return null;
        }

        if (empty(trim($title))) {
            return null;
        }

        return trim($title);
    }

    protected function getSEOTitlePostfix(): string
    {
        return sprintf(' | %s', flexiblePagesSetting(Settings::SETTING_SITE_TITLE));
    }
}
