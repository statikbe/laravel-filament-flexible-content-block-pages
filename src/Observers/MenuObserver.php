<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Statikbe\FilamentFlexibleContentBlockPages\Components\Menu as MenuComponent;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;

/**
 * Clear menu cache
 */
class MenuObserver
{
    public function updated(Menu $menu): void
    {
        MenuComponent::clearMenuCache($menu->code);
    }

    public function deleted(Menu $menu): void
    {
        MenuComponent::clearMenuCache($menu->code);
    }
}
