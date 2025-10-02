<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Statikbe\FilamentFlexibleContentBlockPages\Components\Menu as MenuComponent;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

/**
 * Clear menu cache
 */
class MenuItemObserver
{
    public function updated(MenuItem $menuItem): void
    {
        MenuComponent::clearMenuCache($menuItem->menu->code);
    }

    public function deleted(MenuItem $menuItem): void
    {
        MenuComponent::clearMenuCache($menuItem->menu->code);
    }
}
