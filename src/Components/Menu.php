<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;

class Menu extends Component
{
    public ?Menu $menu;

    public ?Collection $items;

    public string $locale;

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
        $package = FilamentFlexibleContentBlockPagesServiceProvider::PACKAGE_PREFIX;
        $template = "$package::{$theme}.components.menu.{$this->style}";

        // Check if the themed style template exists, otherwise try default style in theme
        if (view()->exists($template)) {
            return view($template);
        }

        $defaultTemplate = "{$package}::{$theme}.components.menu.default";

        return view($defaultTemplate);
    }

    protected function getMenuByCode(string $code): ?Menu
    {
        $menuModel = FilamentFlexibleContentBlockPages::config()->getMenuModel();

        return $menuModel::getByCode($code);
    }

    protected function getMenuItems($menu, ?string $locale = null): Collection
    {
        if (! $menu) {
            return collect();
        }

        $maxDepth = $menu->getEffectiveMaxDepth();
        $eagerLoadRelations = $this->buildEagerLoadRelations($maxDepth);

        // Get only top-level menu items with their visible children based on max depth
        return $menu->menuItems()
            ->with($eagerLoadRelations)
            ->visible()
            ->ordered()
            ->get();
    }

    protected function buildEagerLoadRelations(int $maxDepth): array
    {
        $relations = ['linkable', 'linkable.parent', 'linkable.parent.parent'];
        $currentPath = '';
        $depth = 1;

        while ($depth <= $maxDepth) {
            $currentPath .= $depth === 1 ? 'children' : '.children';
            $relations[$currentPath] = function ($query) {
                $query->visible()->ordered()->with('linkable', 'linkable.parent', 'linkable.parent.parent');
            };
            $depth++;
        }

        return $relations;
    }
}
