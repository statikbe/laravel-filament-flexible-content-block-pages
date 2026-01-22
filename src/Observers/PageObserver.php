<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class PageObserver
{
    /**
     * Checks if the page is used in a menu item or undeletable and blocks deletion.
     */
    public function deleting(Page $page): bool
    {
        if ($page->menuItem || $page->is_undeletable) {
            return false;
        }

        return true;
    }
}
