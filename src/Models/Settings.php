<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\HtmlableMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Cache\TaggableCache;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Observers\SettingsObserver;
use Statikbe\FilamentFlexibleContentBlocks\Facades\FilamentFlexibleContentBlocks;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasMediaAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedMediaTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasTranslatableMedia;
use Statikbe\FilamentFlexibleContentBlocks\Models\Enums\ImageFormat;

/**
 * @property string $site_title
 * @property string $contact_info
 * @property string $footer_copyright
 * @property Media|null $defaultSeoImage
 */
#[ObservedBy(SettingsObserver::class)]
class Settings extends Model implements HasMedia, HasTranslatableMedia
{
    use HasMediaAttributesTrait;
    use HasTranslatedMediaTrait;
    use HasTranslations;
    use InteractsWithMedia;

    const CACHE_TAG_SETTINGS = 'filament-flexible-content-block-pages::settings';

    const SETTING_SITE_TITLE = 'site_title';

    const SETTING_CONTACT_INFO = 'contact_info';

    const SETTING_FOOTER_COPYRIGHT = 'footer_copyright';

    // MEDIA:
    const COLLECTION_DEFAULT_SEO = 'default_seo';

    const CONVERSION_DEFAULT_SEO = 'default_seo';

    const CONVERSION_THUMB = 'thumbnail';

    protected $translatable = [
        self::SETTING_FOOTER_COPYRIGHT,
        self::SETTING_CONTACT_INFO,
    ];

    protected $guarded = [];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getSettingsTable();
    }

    public static function getSettings(): ?static
    {
        return static::query()->first();
    }

    public function registerMediaCollections(): void
    {
        // default seo:
        $this->addMediaCollection(static::COLLECTION_DEFAULT_SEO)
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(static::CONVERSION_THUMB)
                    ->fit(Fit::Contain, 400, 400);
                $this->addMediaConversion(static::CONVERSION_DEFAULT_SEO)
                    ->format(ImageFormat::WEBP->value)
                    ->fit(Fit::Crop, 1200, 630);
            });
    }

    public static function setting(string $settingField, ?string $locale = null): string|bool|null
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $cacheKey = static::getCacheKey($settingField, $locale);
        $settingValue = TaggableCache::rememberForeverWithTag(
            static::CACHE_TAG_SETTINGS,
            $cacheKey,
            function () use ($settingField) {
                $setting = static::getSettings()?->getAttribute($settingField);

                // replace text params in settings if it is a text field (based on $translatable fields):
                if (in_array($settingField, app(static::class)->translatable)) {
                    $setting = FilamentFlexibleContentBlocks::replaceParameters($setting);
                }

                return $setting;
            });

        // get translated value if exists:
        if (is_array($settingValue)) {
            // if no translation is available, return the first value:
            return $settingValue[app()->getLocale()] ?? reset($settingValue);
        }

        return $settingValue;
    }

    public static function getCacheKey(string $settingField, ?string $locale = null): string
    {
        return static::CACHE_TAG_SETTINGS."::{$settingField}__{$locale}";
    }

    public static function imageHtml(string $imageCollection, ?string $imageConversion = null, array $attributes = [], ?string $title = null): ?HtmlableMedia
    {
        $locale = app()->getLocale();
        /* @var Media|null $imageMedia */

        $imageMedia = TaggableCache::rememberForeverWithTag(
            static::CACHE_TAG_SETTINGS,
            static::getCacheKey("image_media__{$imageCollection}", $locale),
            function () use ($imageCollection): ?Media {
                return static::getSettings()->getImageMedia($imageCollection);
            });

        $html = null;

        if ($imageMedia) {
            $html = $imageMedia->img($imageConversion);

            if ($title) {
                $attributes = array_merge([
                    'title' => $title,
                    'alt' => $title,
                ], $attributes);
            }
            $html->attributes($attributes);
        }

        return $html;
    }

    public static function imageUrl(string $imageCollection, ?string $imageConversion = null): ?string
    {
        return TaggableCache::rememberForeverWithTag(
            static::CACHE_TAG_SETTINGS,
            static::getCacheKey("image_url__{$imageCollection}__{$imageConversion}", app()->getLocale()),
            function () use ($imageCollection, $imageConversion) {
                return static::getSettings()->getImageUrl($imageCollection, $imageConversion);
            });
    }

    public function getImageUrl(string $collection, ?string $conversion = null): ?string
    {
        return $this->getFirstMediaUrl($collection, $conversion);
    }

    public function getImage(string $collection, $filters = []): ?Media
    {
        return $this->getFirstMedia($collection, $filters);
    }

    /**
     * Returns the first media for the given collection. First, we check if there is a locale specific version.
     */
    public function getImageMedia(string $collection): ?Media
    {
        $media = $this->getFirstMedia($collection, ['locale' => app()->getLocale()]);
        if (! $media) {
            $media = $this->getFirstMedia($collection);
        }

        return $media;
    }

    public function defaultSeoImage(): MorphMany
    {
        return $this->media()
            ->where('collection_name', static::COLLECTION_DEFAULT_SEO)
            ->where('custom_properties->locale', app()->getLocale());
    }

    public function getMorphClass()
    {
        return flexiblePagesPrefix('settings');
    }
}
