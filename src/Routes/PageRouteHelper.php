<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

/**
 * Handles non-localised URLs.
 */
class PageRouteHelper extends AbstractPageRouteHelper
{
    public function getUrl(Page $page, ?string $locale = null): string
    {
        if ($page->isHomePage()) {
            return route(static::ROUTE_HOME);
        }

        $page->load('parent.parent');

        if ($page->parent && $page->parent->parent) {
            return route(static::ROUTE_GRANDCHILD_PAGE, [
                'grandparent' => $page->parent->parent,
                'parent' => $page->parent,
                'page' => $page,
            ]);
        }

        if ($page->parent) {
            return route(static::ROUTE_CHILD_PAGE, [
                'parent' => $page->parent,
                'page' => $page,
            ]);
        }

        return route(static::ROUTE_PAGE, ['page' => $page]);
    }
}
