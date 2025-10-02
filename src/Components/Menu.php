<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Components\Data\MenuData;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;

class Menu extends Component
{
    const CACHE_MENU_KEY = 'menu_%s_%s';

    public ?MenuData $menu;

    public ?Collection $items;

    public string $locale;

    public string $style;

    public function __construct(
        string $code,
        ?string $style = null,
        ?string $locale = null
    ) {
        $this->locale = $locale ?: app()->getLocale();
        $this->menu = $this->getMenuData($code);

        // Determine the style to use with proper fallback chain
        if ($style) {
            $this->style = $style;
        } else {
            $this->style = $this->getEffectiveStyle();
        }

        $this->items = $this->menu ? $this->menu->items : collect();
    }

    public static function getMenuCacheKey(string $code, string $locale): string
    {
        return flexiblePagesPrefix(sprintf(self::CACHE_MENU_KEY, $locale, $code));
    }

    public static function clearMenuCache(string $code): void
    {
        foreach (FilamentFlexibleContentBlockPages::config()->getSupportedLocales() as $locale) {
            Cache::forget(self::getMenuCacheKey($code, $locale));
        }
    }

    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        $package = FilamentFlexibleContentBlockPagesServiceProvider::PACKAGE_PREFIX;
        $template = "$package::{$theme}.components.menu.{$this->style}";

        // Check if the themed style template exists, otherwise try default style in theme
        if (view()->exists($template)) {
            return view($template);
        }

        $defaultTemplate = "{$package}::{$theme}.components.menu.default";

        return view($defaultTemplate);
    }

    protected function getMenuData(string $code): ?MenuData
    {
        return Cache::rememberForever(self::getMenuCacheKey($code, $this->locale), function () use ($code) {
            $menuModel = FilamentFlexibleContentBlockPages::config()->getMenuModel();
            $menu = $menuModel::getByCode($code);

            if (! $menu) {
                return null;
            }

            return MenuData::create($menu, $this->locale);
        });
    }

    public function getEffectiveStyle(): string
    {
        // Return the menu's style if set, otherwise fall back to config default
        if ($this->menu && ! empty($this->menu->style)) {
            $availableStyles = FilamentFlexibleContentBlockPages::config()->getMenuStyles();
            if (in_array($this->menu->style, $availableStyles)) {
                return $this->menu->style;
            }
        }

        return FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle();
    }
}
