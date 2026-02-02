<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Observers\MenuObserver;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasCodeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasCode;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string|null $title
 * @property string $style
 * @property int|null $max_depth
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Illuminate\Database\Eloquent\Collection<\Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem> $menuItems
 * @property \Illuminate\Database\Eloquent\Collection<\Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem> $allMenuItems
 */
#[ObservedBy(MenuObserver::class)]
class Menu extends Model implements HasCode
{
    use HasCodeTrait;
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'code',
        'description',
        'style',
        'max_depth',
    ];

    protected $translatable = ['title'];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getMenusTable();
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(FilamentFlexibleContentBlockPages::config()->getMenuItemModel()::class)
            ->where('parent_id', \SolutionForest\FilamentTree\Support\Utils::defaultParentId())
            ->orderBy('order');
    }

    public function allMenuItems(): HasMany
    {
        return $this->hasMany(FilamentFlexibleContentBlockPages::config()->getMenuItemModel()::class)
            ->orderBy('order');
    }

    public function getMorphClass()
    {
        return flexiblePagesPrefix('menu');
    }

    public function getEffectiveStyle(): string
    {
        // Return the menu's style if set, otherwise fall back to config default
        if (! empty($this->style)) {
            $availableStyles = FilamentFlexibleContentBlockPages::config()->getMenuStyles();
            if (in_array($this->style, $availableStyles)) {
                return $this->style;
            }
        }

        return FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle();
    }

    public function getEffectiveMaxDepth(): int
    {
        // Return the menu's max_depth if set, otherwise fall back to config default
        return $this->max_depth ?? FilamentFlexibleContentBlockPages::config()->getMenuMaxDepth();
    }

    public function getDisplayTitle(?string $locale = null): ?string
    {
        if (isset($this->title) && ! empty($this->title)) {
            return $this->getTranslation('title', $locale ?: app()->getLocale());
        }

        return null;
    }
}
