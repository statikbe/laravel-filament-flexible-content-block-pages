<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Services\TagPageService;

class SeoTagController extends AbstractSeoPageController
{
    private const MAX_META_DESCRIPTION_LENGTH = 160;

    public function __construct(
        private readonly TagPageService $tagPageService
    ) {}

    public function index(Tag $tag): View
    {
        // Return not found when tag pages are not enabled
        if (FilamentFlexibleContentBlockPages::config()->areTagPagesEnabled()) {
            abort(404);
        }

        // Return not found when the tag type does not have seo pages enabled
        if ($tag->tagType->has_seo_pages) {
            abort(404);
        }

        // Get paginated tagged content with actual model instances
        $taggedContent = $this->tagPageService->getTaggedContent($tag);

        // Get content counts by type for display
        $contentCounts = $this->tagPageService->getContentCounts($tag);

        $modelLabels = $this->createModelLabelsLookup();
        $this->setupSEO($tag, $taggedContent);

        return view('filament-flexible-content-block-pages::tailwind.pages.tag_index', [
            'tag' => $tag,
            'taggedContent' => $taggedContent,
            'contentCounts' => $contentCounts,
            'modelLabels' => $modelLabels,
        ]);
    }

    private function setupSEO(Tag $tag, LengthAwarePaginator $taggedContent): void
    {
        // Set SEO title
        $tagName = $tag->getTranslation('name', app()->getLocale());
        $titlePostfix = ' | '.$this->getSettingsTitle();
        SEOTools::setTitle($tagName.$titlePostfix, false);

        // Set meta description
        $description = $this->generateMetaDescription($tag, $taggedContent);
        SEOTools::setDescription($description);

        // Set canonical URL
        SEOTools::setCanonical(request()->url());

        // Add structured data for the tag page
        $this->addStructuredData($tag, $taggedContent);

        // TODO SEO image from settings, refactor super class to have 1 function to fetch settings SEO img
    }

    private function generateMetaDescription(Tag $tag, LengthAwarePaginator $taggedContent): string
    {
        // Use tag's SEO description if available
        $seoDescription = $tag->getTranslation('seo_description', app()->getLocale());
        if (! empty($seoDescription)) {
            return Str::limit($seoDescription, self::MAX_META_DESCRIPTION_LENGTH);
        }

        $tagName = $tag->getTranslation('name', app()->getLocale());
        $totalItems = $taggedContent->total();

        if ($totalItems === 0) {
            $description = flexiblePagesTrans('tag_pages.meta_description_no_content', ['tag' => $tagName]);
        } else {
            $description = flexiblePagesTrans('tag_pages.meta_description_with_content', [
                'count' => $totalItems,
                'tag' => $tagName,
            ]);
        }

        return Str::limit($description, self::MAX_META_DESCRIPTION_LENGTH);
    }

    private function addStructuredData(Tag $tag, LengthAwarePaginator $taggedContent): void
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $tag->getTranslation('name', app()->getLocale()),
            'description' => $this->generateMetaDescription($tag, $taggedContent),
            'url' => request()->url(),
            'numberOfItems' => $taggedContent->total(),
        ];

        SEOTools::jsonLd()->addValue('structuredData', $structuredData);
    }

    /**
     * Create a lookup array mapping model classes to their Filament resource labels.
     */
    private function createModelLabelsLookup(): array
    {
        $labels = [];
        $enabledModels = FilamentFlexibleContentBlockPages::config()->getTagPageEnabledModels();

        foreach ($enabledModels as $modelClass) {
            $resourceClass = Filament::getModelResource($modelClass);
            /** @var resource|null $resourceClass */
            $labels[$modelClass] = $resourceClass ? $resourceClass::getModelLabel() : class_basename($modelClass);
        }

        return $labels;
    }
}
