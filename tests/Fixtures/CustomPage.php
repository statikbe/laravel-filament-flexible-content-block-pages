<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

/**
 * A custom Page model that extends the package's Page model.
 * This simulates what projects would do when extending the package.
 */
class CustomPage extends Page
{
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return CustomPageFactory::new();
    }
}
