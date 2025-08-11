<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Services\Contracts;

interface GeneratesSitemap
{
    /**
     * Generate the sitemap and save it to the configured location.
     */
    public function generate(): void;
}
