<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;

class Menu extends Component
{
    public $menu;

    public $items;

    public $locale;

    public string $style;

    public function __construct(
        string $code,
        ?string $style = null,
        ?string $locale = null
    ) {
        $this->menu = $this->getMenuByCode($code);
        $this->locale = $locale ?: app()->getLocale();

        // Determine the style to use with proper fallback chain
        if ($style) {
            $this->style = $style;
        } elseif ($this->menu) {
            $this->style = $this->menu->getEffectiveStyle();
        } else {
            $this->style = FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle();
        }

        $this->items = $this->menu ? $this->getMenuItems($this->menu, $this->locale) : [];
    }

    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        $template = "filament-flexible-content-block-pages::{$theme}.components.menu.{$this->style}";

        // Check if the themed style template exists, otherwise try default style in theme
        if (view()->exists($template)) {
            return view($template);
        }

        $defaultTemplate = "filament-flexible-content-block-pages::{$theme}.components.menu.default";
        return view($defaultTemplate);
    }

    protected function getMenuByCode(string $code)
    {
        $menuModel = FilamentFlexibleContentBlockPages::config()->getMenuModel();

        return $menuModel::getByCode($code);
    }

    protected function getMenuItems($menu, ?string $locale = null)
    {
        if (! $menu) {
            return collect();
        }

        return $menu->menuItems()
            ->with('linkable', 'children')
            ->visible()
            ->ordered()
            ->get()
            ->toTree();
    }
}
