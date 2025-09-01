<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Interfaces\LocalizedUrlRoutable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts\HandlesPageRoutes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

/**
 * @property string $name
 * @property string $slug
 * @property string $seo_description
 * @property string $type
 * @property int $order_column
 * @property TagType $tagType
 * @property string $code
 */
class Tag extends \Spatie\Tags\Tag implements Linkable, LocalizedUrlRoutable
{
    public array $translatable = ['name', 'slug', 'seo_description'];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getTagsTable();
    }

    public function tagType(): BelongsTo
    {
        return $this->belongsTo(FilamentFlexibleContentBlockPages::config()->getTagTypeModel()::class, 'type', 'code');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getLocalizedRouteKey($locale)
    {
        return $this->getTranslation('slug', $locale);
    }

    /**
     * This method is overwritten to make filament resolve the model with a translated slug key.
     * {@inheritDoc}
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if (! $this->isTranslatableAttribute($field)) {
            return parent::resolveRouteBindingQuery($query, $value, $field);
        }

        return $query->where(function (Builder $query) use ($field, $value) {
            foreach (array_keys(LaravelLocalization::getSupportedLocales()) as $locale) {
                $query->orWhere("{$field}->{$locale}", $value);
            }

            return $query;
        });
    }

    public function getViewUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return route(HandlesPageRoutes::ROUTE_SEO_TAG_PAGE, [
            'tag' => $this->getTranslation('slug', $locale) ?? $this->slug,
        ]);
    }

    public function getPreviewUrl(?string $locale = null): string
    {
        return $this->getViewUrl($locale);
    }

    public function getMorphClass()
    {
        return flexiblePagesPrefix('tag');
    }
}
