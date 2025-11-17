<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components\Data;

use Illuminate\Support\Collection;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

class MenuData
{
    public function __construct(
        public string $name,
        public ?string $title,
        public ?string $style,
        public Collection $items,
    ) {}

    public static function create(Menu $menu, string $locale): self
    {
        $maxDepth = $menu->getEffectiveMaxDepth();

        // eager load relationships:
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

        // Get only top-level menu items with their visible children based on max depth
        /** @phpstan-ignore-next-line method.notFound */
        $menuItemsData = $menu->menuItems()
            ->with($relations)
            ->visible()
            ->ordered()
            ->get()
            ->map(function (MenuItem $item) use ($locale) {
                return MenuItemData::create($item, $locale);
            });

        return new self(
            $menu->name,
            $menu->getDisplayTitle($locale),
            $menu->style,
            $menuItemsData,
        );
    }
}
