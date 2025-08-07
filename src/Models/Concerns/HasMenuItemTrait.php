<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

/**
 * @property ?MenuItem $menuItem
 *
 * @mixin Model
 */
trait HasMenuItemTrait
{
    public function menuItem(): MorphOne
    {
        return $this->morphOne(MenuItem::class, 'linkable', 'linkable_type', 'linkable_id');
    }
}
