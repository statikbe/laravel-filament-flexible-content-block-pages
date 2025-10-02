<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class LanguageSwitch extends Component
{
    public ?Page $page;

    public function __construct()
    {
        $this->page = $this->getPage();
    }

    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        $package = FilamentFlexibleContentBlockPagesServiceProvider::PACKAGE_PREFIX;
        $template = "{$package}::{$theme}.components.language-switch";

        // Check if the themed template exists, otherwise fallback to tailwind theme
        if (view()->exists($template)) {
            return view($template);
        }

        // Final fallback to tailwind theme
        /** @var view-string $fallbackTemplate */
        $fallbackTemplate = "{$package}::tailwind.components.language-switch";

        return view($fallbackTemplate);
    }

    public function shouldRender(): bool
    {
        return count(LaravelLocalization::getSupportedLocales()) > 1;
    }

    protected function getPage(): Page
    {
        $page = Route::current()->parameter('page');
        if (! $page) {
            if (Route::current()->getName() === 'home') {
                $page = Page::getByCode(Page::HOME_PAGE);
            }
        }

        return $page;
    }
}
