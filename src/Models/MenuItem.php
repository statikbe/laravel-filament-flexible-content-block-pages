<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use SolutionForest\FilamentTree\Concern\ModelTree;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

class MenuItem extends Model
{
    use HasFactory;
    use HasTranslations;
    use ModelTree;

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

    public $translatable = ['label'];

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
        return 'filament-flexible-content-block-pages::menu-item';
    }

    public function getUrl(?string $locale = null): ?string
    {
        // If it has a linkable model, get URL from that
        if ($this->linkable && $this->linkable instanceof Linkable) {
            return $this->linkable->getViewUrl($locale);
        }

        // Otherwise return the custom URL
        return $this->url;
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
}
