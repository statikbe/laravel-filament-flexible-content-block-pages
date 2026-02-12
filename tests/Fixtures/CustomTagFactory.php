<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures;

use Statikbe\FilamentFlexibleContentBlockPages\Database\Factories\TagFactory;

/**
 * Factory for CustomTag model.
 * Extends the package's TagFactory to inherit all states.
 */
class CustomTagFactory extends TagFactory
{
    protected $model = CustomTag::class;
}
