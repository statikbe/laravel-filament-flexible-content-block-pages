<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\MediaLibrary\HasMedia;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasAuthorAttributeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasCodeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasDefaultContentBlocksTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasParentTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedContentBlocksTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedHeroImageAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedIntroAttributeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedOverviewAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedPageAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedSEOAttributesTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasTranslatedSlugAttributeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasCode;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasContentBlocks;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasHeroImageAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasIntroAttribute;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasMediaAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasOverviewAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasPageAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasParent;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasSEOAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

class Page extends Model implements HasCode, HasContentBlocks, HasHeroImageAttributes, HasIntroAttribute, HasMedia, HasMediaAttributes, HasOverviewAttributes, HasPageAttributes, HasParent, HasSEOAttributes, Linkable
{
    use HasAuthorAttributeTrait;
    use HasAuthorAttributeTrait;
    use HasCodeTrait;
    use HasDefaultContentBlocksTrait;
    use HasFactory;
    use HasParentTrait;
    use HasTranslatedContentBlocksTrait;
    use HasTranslatedHeroImageAttributesTrait;
    use HasTranslatedIntroAttributeTrait;
    use HasTranslatedOverviewAttributesTrait;
    use HasTranslatedPageAttributesTrait;
    use HasTranslatedSEOAttributesTrait;
    use HasTranslatedSlugAttributeTrait;

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getPagesTable();
    }

    public function getViewUrl(?string $locale = null): string
    {
        // toggle the locale to make sure the slug gets translated:
        /*$currentLocale = app()->getLocale();
        $locale = $locale ?? $currentLocale;

        $url = LaravelLocalization::getLocalizedUrl($locale, route('home_index')) . '/' . $this->translate('slug', $locale);

        return $url;*/

        // TODO
        return 'https://www.google.com';
    }

    public function getPreviewUrl(?string $locale = null): string
    {
        return $this->getViewUrl($locale);
    }

    public static function getUrl(string $code, ?string $locale = null): ?string
    {
        return static::code($code)
            ->first()
            ?->getViewUrl($locale);
    }
}
