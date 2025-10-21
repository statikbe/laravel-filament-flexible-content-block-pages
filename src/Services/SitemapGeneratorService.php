<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator as SpatieSitemapGenerator;
use Spatie\Sitemap\Tags\Alternate;
use Spatie\Sitemap\Tags\Url;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Services\Contracts\GeneratesSitemap;
use Statikbe\FilamentFlexibleContentBlockPages\Services\Enum\SitemapGeneratorMethod;
use Statikbe\FilamentFlexibleContentBlocks\ContentBlocks\CallToActionBlock;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasCode;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasParent;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

class SitemapGeneratorService implements GeneratesSitemap
{
    protected Sitemap $sitemap;

    protected string $canonicalLocale;

    protected array $supportedLocales;

    public function __construct()
    {
        $this->sitemap = Sitemap::create();

        $this->canonicalLocale = FilamentFlexibleContentBlockPages::config()->getSEODefaultCanonicalLocale();
        $this->supportedLocales = FilamentFlexibleContentBlockPages::config()->getSupportedLocales();
    }

    public function generate(): void
    {
        if (! FilamentFlexibleContentBlockPages::config()->isSitemapEnabled()) {
            return;
        }

        $method = FilamentFlexibleContentBlockPages::config()->getSitemapMethod();
        if ($method === SitemapGeneratorMethod::CRAWL) {
            $this->generateByCrawling();
        } elseif ($method === SitemapGeneratorMethod::HYBRID) {
            $this->generateByCrawling();
            $this->generateManually();
        } else {
            $this->generateManually();
        }

        $this->writeSitemap();
    }

    protected function generateManually(): void
    {
        if (FilamentFlexibleContentBlockPages::config()->shouldIncludePagesInSitemap()) {
            $this->addPages();
        }

        if (FilamentFlexibleContentBlockPages::config()->shouldIncludeLinkRoutesInSitemap()) {
            $this->addLinkRoutes();
        }

        if (FilamentFlexibleContentBlockPages::config()->shouldIncludeLinkableModelsInSitemap()) {
            $this->addLinkableModels();
        }

        if (FilamentFlexibleContentBlockPages::config()->areTagPagesEnabled()) {
            $this->addSeoTagPages();
        }

        $this->addCustomUrls();
    }

    protected function generateByCrawling(): void
    {
        $baseUrl = config('app.url');

        $this->sitemap = SpatieSitemapGenerator::create($baseUrl)->getSitemap();
    }

    protected function addPages(): void
    {
        $pageModel = FilamentFlexibleContentBlockPages::config()->getPageModel();

        $pages = $pageModel::query()
            ->published()
            ->get();

        foreach ($pages as $page) {
            $this->addPageToSitemap($page);
        }
    }

    protected function addPageToSitemap(Page $page): void
    {
        // Get canonical URL
        $canonicalUrl = $page->getViewUrl($this->canonicalLocale);

        $lastModified = $page->{$page->getUpdatedAtColumn()} ?? $page->{$page->getCreatedAtColumn()};

        $urlTag = $this->addToSitemap($canonicalUrl,
            $lastModified,
            $this->calculatePriority($page),
            $this->calculateChangeFrequency($page),
            onlyCreate: true);

        $this->addAlternativeLocaleUrls($urlTag, $page);

        $this->sitemap->add($urlTag);
    }

    protected function addLinkRoutes(): void
    {
        try {
            $linkRoutes = FilamentFlexibleBlocksConfig::getLinkRoutes();
            $excludePatterns = FilamentFlexibleContentBlockPages::config()->getSitemapExcludePatterns();

            foreach ($linkRoutes as $routeName => $routeUri) {
                if ($this->shouldExcludeUrl($routeUri, $excludePatterns)) {
                    continue;
                }

                $fullUrl = url($routeUri);

                $this->addToSitemap($fullUrl, null, 0.5, Url::CHANGE_FREQUENCY_MONTHLY);
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            // cannot add routes to sitemap, so continue...
            report($e);
        }
    }

    protected function addLinkableModels(): void
    {
        $linkableModels = $this->getLinkableModels();

        foreach ($linkableModels as $modelClass) {
            if (! class_exists($modelClass)) {
                continue;
            }

            // Skip Page model as it's handled separately
            if (is_a($modelClass, Page::class, true)) {
                continue;
            }

            $modelQuery = $modelClass::query();

            if (method_exists($modelClass, 'scopePublished')) {
                $modelQuery->published();
            }

            $models = $modelQuery->get();

            foreach ($models as $model) {
                if (method_exists($model, 'getViewUrl')) {
                    /** @var Linkable $model */
                    $url = $model->getViewUrl($this->canonicalLocale);

                    $urlTag = $this->addToSitemap($url,
                        $model->updated_at ?? $model->created_at ?? Carbon::now(),
                        0.6,
                        Url::CHANGE_FREQUENCY_WEEKLY,
                        onlyCreate: true);

                    $this->addAlternativeLocaleUrls($urlTag, $model);

                    $this->sitemap->add($urlTag);
                }
            }
        }
    }

    protected function addCustomUrls(): void
    {
        $customUrls = FilamentFlexibleContentBlockPages::config()->getSitemapCustomUrls();

        foreach ($customUrls as $url) {
            $this->sitemap->add(
                Url::create($url)
                    ->setPriority(0.5)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        }
    }

    protected function addSeoTagPages(): void
    {
        $seoTags = Tag::whereRelation('tagType', 'has_seo_pages', true)->get();

        foreach ($seoTags as $tag) {
            $this->sitemap->add(
                Url::create($tag->getViewUrl($this->canonicalLocale))
            );

            $this->addToSitemap($tag->getViewUrl($this->canonicalLocale),
                null, // TODO query the last updated_at of all models.
                0.6,
                Url::CHANGE_FREQUENCY_WEEKLY);
        }
    }

    protected function getLinkableModels(): array
    {
        $ctaModels = collect(FilamentFlexibleBlocksConfig::getCallToActionModels(CallToActionBlock::class))
            ->map(function (array|string $model): string {
                return \is_array($model) ? $model['model'] : $model;
            })
            ->toArray();
        $menuModels = FilamentFlexibleContentBlockPages::config()->getMenuLinkableModels();

        // Merge and remove duplicates
        return array_unique(array_merge($ctaModels, $menuModels));
    }

    protected function calculatePriority(HasParent&HasCode&Model $page): float
    {
        // Homepage gets highest priority
        if ($page->hasAttribute('code') && $page->getAttribute('code') === Page::HOME_PAGE) {
            return 1.0;
        }

        // Parent pages get higher priority than children
        if ($page->hasAttribute('parent_id') && ! $page->getAttribute('parent_id')) {
            return 0.8;
        }

        // Child pages
        return 0.6;
    }

    protected function calculateChangeFrequency(Model $page): string
    {
        $updatedAt = $page->{$page->getUpdatedAtColumn()};
        $daysSinceUpdate = $updatedAt ? $updatedAt->diffInDays(Carbon::now()) : 365;

        if ($daysSinceUpdate < 7) {
            return Url::CHANGE_FREQUENCY_WEEKLY;
        }

        if ($daysSinceUpdate < 30) {
            return Url::CHANGE_FREQUENCY_MONTHLY;
        }

        return Url::CHANGE_FREQUENCY_YEARLY;
    }

    protected function shouldExcludeUrl(string $url, array $excludePatterns): bool
    {
        foreach ($excludePatterns as $pattern) {
            if (fnmatch($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    protected function addToSitemap(string $url, ?Carbon $lastModifiedAt, float $priority, string $frequency, bool $onlyCreate = false): Url
    {
        $urlTag = Url::create($url)
            ->setPriority($priority)
            ->setChangeFrequency($frequency);

        if ($lastModifiedAt) {
            $urlTag->setLastModificationDate($lastModifiedAt);
        }

        if (! $onlyCreate) {
            $this->sitemap->add($urlTag);
        }

        return $urlTag;
    }

    protected function addAlternativeLocaleUrls(Url &$urlTag, Linkable $page): void
    {
        // Add alternate URLs for other locales
        foreach ($this->supportedLocales as $locale) {
            if ($locale !== $this->canonicalLocale) {
                $alternateUrl = $page->getViewUrl($locale);
                $urlTag->addAlternate($alternateUrl, $locale);
            }
        }
    }

    protected function writeSitemap(): void
    {
        $this->sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
