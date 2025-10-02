<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Components\Data\MenuItemData;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;

class MenuItem extends Component
{
    public MenuItemData $item;

    public string $style;

    public ?string $locale;

    public function __construct(
        MenuItemData $item,
        ?string $style = null,
        ?string $locale = null
    ) {
        $this->item = $item;
        $this->style = $style ?: FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle();
        $this->locale = $locale ?: app()->getLocale();
    }

    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        $package = FilamentFlexibleContentBlockPagesServiceProvider::PACKAGE_PREFIX;
        $template = "{$package}::{$theme}.components.menu.{$this->style}-item";

        // Check if the themed style item template exists, otherwise try default item in theme
        if (view()->exists($template)) {
            return view($template);
        }

        $defaultTemplate = "{$package}::{$theme}.components.menu.default-item";

        return view($defaultTemplate);
    }
}
