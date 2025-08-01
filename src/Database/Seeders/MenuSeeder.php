<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

/**
 * Base class to easily build your own menu seeders.
 */
abstract class MenuSeeder extends Seeder
{
    /**
     * Create a new menu with typed arguments and sane defaults.
     */
    protected function createMenu(
        string $name,
        string $code,
        ?string $description = null,
        ?string $style = null,
        ?int $maxDepth = null
    ): Menu {
        $menuModel = FilamentFlexibleContentBlockPages::config()->getMenuModel();

        return $menuModel::create([
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'style' => $style ?? FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle(),
            'max_depth' => $maxDepth, // null will use config default via getEffectiveMaxDepth()
        ]);
    }

    /**
     * Create a base menu item with common fields.
     */
    protected function createMenuItem(
        Menu|int $menu,
        string|array $label,
        bool $isVisible = true,
        string $target = '_self',
        ?string $icon = null,
        MenuItem|int $parent = -1
    ): MenuItem {
        $menuItemModel = FilamentFlexibleContentBlockPages::config()->getMenuItemModel();
        $menuId = $menu->id ?? $menu;
        $parentId = $parent->id ?? $parent;

        return $menuItemModel::create([
            'menu_id' => $menuId,
            'link_type' => null, // Will be set by specific type methods
            'label' => $this->normalizeLabel($label),
            'is_visible' => $isVisible,
            'target' => $target,
            'icon' => $icon,
            'parent_id' => $parentId,
            'order' => $this->getNextOrder($menuId, $parentId),
        ]);
    }

    /**
     * Create a menu item that links to a Laravel route.
     */
    protected function createMenuItemForRoute(
        Menu|int $menu,
        string $route,
        string|array $label,
        bool $isVisible = true,
        string $target = '_self',
        ?string $icon = null,
        MenuItem|int $parent = -1
    ): MenuItem {
        $this->validateRoute($route);

        $menuItem = $this->createMenuItem($menu, $label, $isVisible, $target, $icon, $parent);

        $menuItem->update([
            'link_type' => MenuItem::LINK_TYPE_ROUTE,
            'route' => $route,
        ]);

        return $menuItem;
    }

    /**
     * Create a menu item that links to an external URL.
     */
    protected function createMenuItemForUrl(
        Menu|int $menu,
        string $url,
        string|array $label,
        bool $isVisible = true,
        string $target = '_blank', // External URLs default to new tab
        ?string $icon = null,
        MenuItem|int $parent = -1
    ): MenuItem {
        $menuItem = $this->createMenuItem($menu, $label, $isVisible, $target, $icon, $parent);

        $menuItem->update([
            'link_type' => MenuItem::LINK_TYPE_URL,
            'url' => $url,
        ]);

        return $menuItem;
    }

    /**
     * Create a menu item that links to a model (Page, etc.).
     */
    protected function createMenuItemForModel(
        Menu|int $menu,
        Model $model,
        string|array|null $label = null,
        bool $useModelTitle = true,
        bool $isVisible = true,
        string $target = '_self',
        ?string $icon = null,
        MenuItem|int $parent = -1
    ): MenuItem {
        // If no label provided and useModelTitle is true, try to get model menu label
        if ($label === null && $useModelTitle && $model instanceof HasMenuLabel) {
            $label = $model->getMenuLabel();
        }

        $menuItem = $this->createMenuItem($menu, $label, $isVisible, $target, $icon, $parent);

        $menuItem->update([
            'link_type' => 'model', // Assuming this is the linkable type
            'use_model_title' => $useModelTitle,
        ]);

        // Set the polymorphic relationship
        $menuItem->linkable()->associate($model);
        $menuItem->save();

        return $menuItem;
    }

    /**
     * Convert string labels to translatable array format.
     */
    private function normalizeLabel(string|array $label): array
    {
        if (is_string($label)) {
            return [app()->getLocale() => $label];
        }

        return $label;
    }

    /**
     * Get the next order value for items within the same menu/parent scope.
     */
    private function getNextOrder(int $menuId, int $parentId): int
    {
        $menuItemModel = FilamentFlexibleContentBlockPages::config()->getMenuItemModel();

        $maxOrder = $menuItemModel::where('menu_id', $menuId)
            ->where('parent_id', $parentId)
            ->max('order');

        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Validate that a route exists.
     */
    private function validateRoute(string $route): void
    {
        if (! Route::has($route)) {
            throw new InvalidArgumentException("Route '{$route}' does not exist.");
        }
    }
}
