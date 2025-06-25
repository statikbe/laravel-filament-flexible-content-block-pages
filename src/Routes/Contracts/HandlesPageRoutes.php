<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

interface HandlesPageRoutes
{
    /**
     * Create the page routes. Routes needed:
     * - home page
     * - page
     * - page with parent
     * - page with grandparent
     */
    public function defineRoutes(): void;

    /**
     * Returns the url of the given page in the given locale.
     */
    public function getUrl(Page $page, ?string $locale = null): string;
}
