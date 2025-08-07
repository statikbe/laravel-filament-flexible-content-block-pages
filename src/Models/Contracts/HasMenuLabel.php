<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasMenuItemTrait;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasTitleMenuLabelTrait;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

/**
 * @see HasMenuItemTrait for implementation
 * @see HasTitleMenuLabelTrait for implementation
 *
 * @property ?MenuItem $menuItem
 */
interface HasMenuLabel extends Linkable
{
    /**
     * Sets the menu item morph relationship.
     *
     * @see HasMenuItemTrait for a convenient implementation.
     */
    public function menuItem(): MorphOne;

    /**
     * Get the display label for menu items.
     * This method should return a translatable field value or fallback text.
     */
    public function getMenuLabel(?string $locale = null): string;

    /**
     * Scope to search for models that can be used in menu items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search  The search term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchForMenuItems($query, string $search);
}
