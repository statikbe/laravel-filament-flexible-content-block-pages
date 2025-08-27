<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;

interface HandlesPageRoutes
{
    public const ROUTE_PAGE = 'filament-flexible-content-block-pages::page_index';

    public const ROUTE_CHILD_PAGE = 'filament-flexible-content-block-pages::child_page_index';

    public const ROUTE_SEO_TAG_PAGE = 'filament-flexible-content-block-pages::seo_tag_page_index';

    public const ROUTE_HOME = 'home';

    public const ROUTE_GRANDCHILD_PAGE = 'filament-flexible-content-block-pages::grandchild_page_index';

    /**
     * Creates the page and SEO tag routes.
     */
    public function defineRoutes(): void;

    /**
     * Create the page routes. Routes needed:
     * - home page
     * - page
     * - page with parent
     * - page with grandparent
     */
    public function definePageRoutes(): void;

    /**
     * Create the routes for the SEO tag pages.
     */
    public function defineSeoTagRoutes(): void;

    /**
     * Returns the url of the given page in the given locale.
     */
    public function getUrl(Page $page, ?string $locale = null): string;

    public function getTagPageUrl(Tag $tag, ?string $locale = null): string;
}
