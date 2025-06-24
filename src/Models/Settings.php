<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\HtmlableMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasMediaAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Facades\FilamentFlexibleContentBlocks;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedMediaTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasTranslatableMedia;
use Statikbe\FilamentFlexibleContentBlocks\Models\Enums\ImageFormat;

class Settings extends Model implements HasMedia, HasTranslatableMedia
{
    use HasMediaAttributes;
    use HasTranslatedMediaTrait;
    use HasTranslations;
    use InteractsWithMedia;

    const CACHE_SETTINGS = 'settings';

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

    public static function getSettings(): ?self
    {
        return Settings::first();
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

        $settingValue = Cache::tags([static::CACHE_SETTINGS])->rememberForever("settings::setting__{$settingField}_{$locale}", function () use ($settingField) {
            $setting = static::getSettings()->getAttribute($settingField);

            // replace text params in settings if it is a text field (based on $translatable fields):
            if (in_array($settingField, (new Settings)->translatable)) {
                $setting = FilamentFlexibleContentBlocks::replaceParameters($setting);
            }

            return $setting;
        });

        // get translated value if exists:
        if (is_array($settingValue)) {
            // if no translation is available return the first value:
            return $settingValue[app()->getLocale()] ?? reset($settingValue);
        }

        return $settingValue;
    }

    public static function imageHtml(string $imageCollection, ?string $imageConversion = null, array $attributes = [], ?string $title = null): ?HtmlableMedia
    {
        $locale = app()->getLocale();
        /* @var Media|null $imageMedia */
        $imageMedia = Cache::tags([static::CACHE_SETTINGS])->rememberForever(
            "filament-flexible-content-block-pages::settings::image_media__{$imageCollection}__{$locale}",
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
        return Cache::tags([self::CACHE_SETTINGS])->rememberForever(
            "filament-flexible-content-block-pages::settings::image_url__{$imageCollection}__{$imageConversion}",
            function () use ($imageCollection, $imageConversion) {
                return static::getSettings()->getImageUrl($imageCollection, $imageConversion);
            });
    }

    public function getImageUrl(string $collection, ?string $conversion = null): ?string
    {
        return $this->getFirstMediaUrl($collection, $conversion);
    }

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
}
