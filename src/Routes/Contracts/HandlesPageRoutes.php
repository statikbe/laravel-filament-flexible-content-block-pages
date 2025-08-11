<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;

interface HandlesPageRoutes
{
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
