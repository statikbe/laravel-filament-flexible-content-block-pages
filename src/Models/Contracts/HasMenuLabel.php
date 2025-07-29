<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts;

use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

interface HasMenuLabel extends Linkable
{
    /**
     * Get the display label for menu items.
     * This method should return a translatable field value or fallback text.
     */
    public function getMenuLabel(?string $locale = null): string;

    /**
     * Scope to search for models that can be used in menu items.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search The search term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchForMenuItems($query, string $search);
}