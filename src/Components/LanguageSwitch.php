<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class LanguageSwitch extends Component
{
    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        $template = "filament-flexible-content-block-pages::components.{$theme}.language-switch";

        // Check if the themed template exists, otherwise fallback to tailwind theme
        if (view()->exists($template)) {
            return view($template);
        }

        // Final fallback to tailwind theme
        return view('filament-flexible-content-block-pages::components.tailwind.language-switch');
    }

    public function shouldRender(): bool
    {
        return ! empty(LaravelLocalization::getSupportedLocales());
    }
}
