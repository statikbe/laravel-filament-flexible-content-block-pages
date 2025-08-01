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
        $template = "filament-flexible-content-block-pages::{$theme}.components.language-switch";

        // Check if the themed template exists, otherwise fallback to tailwind theme
        if (view()->exists($template)) {
            return view($template);
        }

        // Final fallback to tailwind theme
        /** @var view-string $fallbackTemplate */
        $fallbackTemplate = 'filament-flexible-content-block-pages::tailwind.components.language-switch';
        return view($fallbackTemplate);
    }

    public function shouldRender(): bool
    {
        return count(LaravelLocalization::getSupportedLocales()) > 1;
    }
}
