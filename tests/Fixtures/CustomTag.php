<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;

/**
 * A custom Tag model that extends the package's Tag model.
 * This simulates what projects would do when extending the package.
 */
class CustomTag extends Tag
{
    /**
     * Override the morph class to use a custom identifier.
     * This is a common pattern when extending package models.
     */
    public function getMorphClass(): string
    {
        return 'custom-tag';
    }

    /**
     * A custom method to verify the correct class is instantiated.
     */
    public function isCustomTag(): bool
    {
        return true;
    }
}
