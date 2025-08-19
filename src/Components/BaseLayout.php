<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;

class BaseLayout extends Component
{
    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();

        return flexiblePagesView("{$theme}.components.layouts.base");
    }
}
