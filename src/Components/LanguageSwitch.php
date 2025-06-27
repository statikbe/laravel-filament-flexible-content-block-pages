<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LanguageSwitch extends Component
{
    public function render()
    {
        return view('filament-flexible-content-block-pages::components.language-switch');
    }

    public function shouldRender(): bool
    {
        return ! empty(LaravelLocalization::getSupportedLocales());
    }
}
