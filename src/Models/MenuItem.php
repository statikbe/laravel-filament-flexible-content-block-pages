<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;
use Studio15\FilamentTree\Concerns\InteractsWithTree;

class MenuItem extends Model
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithTree;
    use NodeTrait;

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
        '_lft',
        '_rgt',
        'parent_id',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'use_model_title' => 'boolean',
    ];

    public $translatable = ['label'];

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

    public function canHaveChildren(): bool
    {
        $maxDepth = FilamentFlexibleContentBlockPages::config()->getMenuMaxDepth();

        return $this->depth < $maxDepth;
    }

    public function getMorphClass()
    {
        return 'filament-flexible-content-block-pages::menu-item';
    }

    public static function getTreeLabelAttribute(): string
    {
        return 'label';
    }

    public function getTreeCaption(): string
    {
        if ($this->linkable_type && $this->linkable) {
            return flexiblePagesTrans('menu_items.tree.linked_to').' '.class_basename($this->linkable_type);
        }

        if ($this->url) {
            return flexiblePagesTrans('menu_items.tree.external_url').': '.$this->url;
        }

        if ($this->route) {
            return flexiblePagesTrans('menu_items.tree.route').': '.$this->route;
        }

        return flexiblePagesTrans('menu_items.tree.no_link');
    }

    public function getScopeAttributes(): array
    {
        return ['menu_id'];
    }
}
