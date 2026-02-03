<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Tests\Fixtures;

use Statikbe\FilamentFlexibleContentBlockPages\Database\Factories\PageFactory;

/**
 * Factory for CustomPage model.
 * Extends the package's PageFactory to inherit all states.
 */
class CustomPageFactory extends PageFactory
{
    protected $model = CustomPage::class;
}
