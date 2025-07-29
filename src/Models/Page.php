<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
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

/**
 * @property bool $is_undeletable
 */
class Page extends Model implements HasCode, HasContentBlocks, HasHeroImageAttributes, HasIntroAttribute, HasMedia, HasMediaAttributes, HasMenuLabel, HasOverviewAttributes, HasPageAttributes, HasParent, HasSEOAttributes
{
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

    public static function getUrl(string $code, ?string $locale = null): ?string
    {
        return static::code($code)
            ->first()
            ?->getViewUrl($locale);
    }

    public function isHomePage(): bool
    {
        return $this->code === static::HOME_PAGE;
    }

    public function isDeletable(): bool
    {
        // TODO improve once the authorisation is implemented:
        return ! $this->is_undeletable;
    }

    public function getMorphClass()
    {
        return 'filament-flexible-content-block-pages::page';
    }

    public function getMenuLabel(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $this->getTranslation('title', $locale) ?: $this->getTranslation('title', config('app.fallback_locale', 'en'));
    }

    public function scopeSearchForMenuItems($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('intro', 'like', "%{$search}%")
                ->orWhere('overview_title', 'like', "%{$search}%");
        });
    }
}
