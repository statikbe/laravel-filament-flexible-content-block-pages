<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Exception;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use SolutionForest\FilamentTree\Concern\ModelTree;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlockPages\Observers\MenuItemObserver;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

/**
 * @property int $id
 * @property int $menu_id
 * @property string $link_type
 * @property array $label
 * @property string|null $url
 * @property string|null $route
 * @property string|null $linkable_type
 * @property int|null $linkable_id
 * @property string $target
 * @property string|null $icon
 * @property bool $is_visible
 * @property bool $use_model_title
 * @property int $order
 * @property int $parent_id
 * @property Collection<int, MenuItem> $children
 * @property Model|null $linkable
 * @property Menu $menu
 */
#[ObservedBy(MenuItemObserver::class)]
class MenuItem extends Model
{
    use HasFactory;
    use HasTranslations;
    use ModelTree;

    const LINK_TYPE_URL = 'url';

    const LINK_TYPE_ROUTE = 'route';

    protected $fillable = [
        'menu_id',
        'link_type',
        'label',
        'url',
        'route',
        'linkable_type',
        'linkable_id',
        'target',
        'icon',
        'is_visible',
        'use_model_title',
        'order',
        'parent_id',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'use_model_title' => 'boolean',
        'parent_id' => 'int',
        'order' => 'int',
    ];

    public $translatable = [
        'label',
        'url',
    ];

    /*
     * The filament-tree package iterates over the tree and it is too complicated to implement eager fetching
     * on a recursive children relationship.
     */
    protected $with = ['linkable'];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuItemsTable();
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(FilamentFlexibleContentBlockPages::config()->getMenuModel()::class);
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo('linkable', 'linkable_type', 'linkable_id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, $this->determineParentColumnName());
    }

    public function getMorphClass()
    {
        return flexiblePagesPrefix('menu-item');
    }

    public function getUrl(?string $locale = null): ?string
    {
        // If it has a linkable model, get URL from that
        if ($this->linkable && $this->linkable instanceof Linkable) {
            return $this->linkable->getViewUrl($locale);
        }

        // Otherwise return the custom URL
        return $this->translate('url', $locale);
    }

    public function getDisplayLabel(?string $locale = null): string
    {
        // If configured to use model title and has linkable model
        if ($this->use_model_title && $this->linkable) {
            // Only use HasMenuLabel interface if available
            if ($this->linkable instanceof HasMenuLabel) {
                return $this->linkable->getMenuLabel($locale);
            }

            // If no interface, fall back to custom label
            return $this->getTranslation('label', $locale ?: app()->getLocale());
        }

        // Use custom label
        return $this->getTranslation('label', $locale ?: app()->getLocale());
    }

    public function getTarget(): string
    {
        return $this->target ?: '_self';
    }

    public function isVisible(): bool
    {
        return $this->is_visible;
    }

    public function getCompleteUrl(?string $locale = null): string
    {
        switch ($this->link_type) {
            case self::LINK_TYPE_ROUTE:
                try {
                    $routeName = $this->route ?? '';
                    if (empty($routeName)) {
                        return '#';
                    }

                    $parameters = []; // Route parameters not currently supported in schema

                    return route($routeName, $parameters);

                } catch (Exception $e) {
                    return '#';
                }
            default:
                return $this->getUrl($locale) ?? '#';
        }
    }

    public function isCurrentMenuItem(): bool
    {
        $currentUrl = request()->url();
        $itemUrl = $this->getCompleteUrl();

        return static::urlsMatch($itemUrl, $currentUrl);
    }

    public static function urlsMatch(string $itemUrl, string $currentUrl): bool
    {
        // Remove trailing slashes for comparison
        $currentUrl = rtrim($currentUrl, '/');
        $itemUrl = rtrim($itemUrl, '/');

        if ($itemUrl === '#' || empty($itemUrl)) {
            return false;
        }

        return $currentUrl === $itemUrl;
    }

    public function hasActiveChildren(): bool
    {
        if (! $this->relationLoaded('children') || $this->children->isEmpty()) {
            return false;
        }

        return $this->children->some(function ($child) {
            /** @var MenuItem $child */
            return $child->isCurrentMenuItem() || $child->hasActiveChildren();
        });
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
