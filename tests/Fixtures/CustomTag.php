<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;

/**
 * A custom Tag model that extends the package's Tag model.
 * This simulates what projects would do when extending the package.
 */
class CustomTag extends Tag
{
    use HasFactory;

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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return CustomTagFactory::new();
    }
}
