<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Statikbe\FilamentFlexibleContentBlockPages\Cache\TaggableCache;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasDatabaseSearchTrait;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasPageTreeTrait;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasTitleMenuLabelTrait;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlockPages\Observers\PageObserver;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasAuthorAttributeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasCodeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasDefaultContentBlocksTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasHeroCallToActionsTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedContentBlocksTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedHeroImageAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedHeroVideoUrlAttributeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedIntroAttributeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedOverviewAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedPageAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedSEOAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedSlugAttributeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasCode;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasContentBlocks;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasHeroCallToActionsAttribute;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasHeroImageAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasHeroVideoAttribute;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasIntroAttribute;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasMediaAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasOverviewAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasPageAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasParent;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasSEOAttributes;

/**
 * @property bool $is_undeletable
 */
#[ObservedBy(PageObserver::class)]
class Page extends Model implements HasCode, HasContentBlocks, HasHeroCallToActionsAttribute, HasHeroImageAttributes, HasHeroVideoAttribute, HasIntroAttribute, HasMedia, HasMediaAttributes, HasMenuLabel, HasOverviewAttributes, HasPageAttributes, HasParent, HasSEOAttributes
{
    use HasAuthorAttributeTrait;
    use HasCodeTrait {
        getByCode as getByCodeFromDatabase;
    }
    use HasDatabaseSearchTrait;
    use HasDefaultContentBlocksTrait;
    use HasFactory;
    use HasHeroCallToActionsTrait;
    use HasPageTreeTrait;
    use HasTitleMenuLabelTrait;
    use HasTranslatedContentBlocksTrait;
    use HasTranslatedHeroImageAttributesTrait;
    use HasTranslatedHeroVideoUrlAttributeTrait;
    use HasTranslatedIntroAttributeTrait;
    use HasTranslatedOverviewAttributesTrait;
    use HasTranslatedPageAttributesTrait;
    use HasTranslatedSEOAttributesTrait;
    use HasTranslatedSlugAttributeTrait;

    const HOME_PAGE = 'HOME';

    protected $fillable = ['is_undeletable'];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getPagesTable();
    }

    public function getViewUrl(?string $locale = null): string
    {
        return FilamentFlexibleContentBlockPages::getUrl($this, $locale);
    }

    public function getPreviewUrl(?string $locale = null): string
    {
        return $this->getViewUrl($locale);
    }

    /**
     * Get the URL of this page. Cached!
     */
    public static function getUrl(string $code, ?string $locale = null): ?string
    {
        $cacheTag = static::getCacheTag($code);

        return TaggableCache::rememberForeverWithTag($cacheTag, $cacheTag.'_url_'.$locale, function () use ($code, $locale) {
            return static::code($code)
                ->first()
                ?->getViewUrl($locale);
        });
    }

    public static function getCacheTag(string $code): string
    {
        return flexiblePagesPrefix('page__code:'.$code);
    }

    /**
     * Retrieve this page by code. The response is cached.
     */
    public static function getByCode(string $code): ?static
    {
        $cacheTag = static::getCacheTag($code);

        return TaggableCache::rememberForeverWithTag($cacheTag, $cacheTag.'_model', function () use ($code) {
            return static::getByCodeFromDatabase($code);
        });
    }

    public function isHomePage(): bool
    {
        return $this->code === static::HOME_PAGE;
    }

    public function isDeletable(): bool
    {
        if (! $this->hasAttribute('is_undeletable')) {
            return true;
        }

        return ! $this->is_undeletable;
    }

    public function getMorphClass()
    {
        return flexiblePagesPrefix('page');
    }

    /**
     * Resolve the route binding to use the configured Page model.
     * This allows projects to extend the Page model and have route model binding
     * return the correct model class instance.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $configuredModel = FilamentFlexibleContentBlockPages::config()->getPageModel();

        // If the configured model is different from this class, delegate to it
        if (get_class($configuredModel) !== static::class) {
            return $configuredModel->resolveRouteBinding($value, $field);
        }

        // Use the default resolution from the trait (searches translated slugs)
        return $this->resolveRouteBindingQuery($this->newQuery(), $value, $field)->first();
    }

    /**
     * Clear cache of this page for all cache items with a tag.
     */
    public function clearCache(): void
    {
        if ($this->code) {
            TaggableCache::flushTag(static::getCacheTag($this->code));
        }
    }
}
