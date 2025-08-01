<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem as MenuItemModel;

class MenuItem extends Component
{
    public MenuItemModel $item;

    public string $style;

    public ?string $locale;

    public function __construct(
        MenuItemModel $item,
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
        $template = "filament-flexible-content-block-pages::{$theme}.components.menu.{$this->style}-item";

        // Check if the themed style item template exists, otherwise try default item in theme
        if (view()->exists($template)) {
            return view($template);
        }

        $defaultTemplate = "filament-flexible-content-block-pages::{$theme}.components.menu.default-item";
        return view($defaultTemplate);
    }

}
