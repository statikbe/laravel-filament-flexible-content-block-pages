<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Services\Enum;

enum SitemapGeneratorMethod
{
    case CRAWL;

    case MANUAL;

    // uses crawler first, then manually defined routes are added.
    case HYBRID;
}
