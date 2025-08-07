<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class PageObserver
{
    /**
     * Checks if the page is used in a menu item and blocks deletion.
     */
    public function deleting(Page $page): bool
    {
        if ($page->menuItem) {
            return false;
        }

        return true;
    }
}
