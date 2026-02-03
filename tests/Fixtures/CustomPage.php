<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

/**
 * A custom Page model that extends the package's Page model.
 * This simulates what projects would do when extending the package.
 */
class CustomPage extends Page
{
    /**
     * Override the morph class to use a custom identifier.
     * This is a common pattern when extending package models.
     */
    public function getMorphClass(): string
    {
        return 'custom-page';
    }

    /**
     * A custom method to verify the correct class is instantiated.
     */
    public function isCustomPage(): bool
    {
        return true;
    }
}
