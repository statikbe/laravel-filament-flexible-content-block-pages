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
        array $title = [],
        ?string $style = null,
        ?int $maxDepth = null
    ): Menu {
        return $this->getMenuModel()::create([
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'title' => ! empty($title) ? $title : null,
            'style' => $style ?? FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle(),
            'max_depth' => $maxDepth, // null will use config default via getEffectiveMaxDepth()
        ]);
    }

    /**
     * Generic function to create a menu item.
     */
    protected function createMenuItem(
        Menu|int $menu,
        string|array $label,
        string $link_type,
        bool $isVisible = true,
        string $target = '_self',
        ?string $icon = null,
        MenuItem|int|null $parent = null,
        /* only for link_type MenuItem::LINK_TYPE_ROUTE */
        ?string $route = null,
        /* only for link_type MenuItem::LINK_TYPE_URL */
        ?array $url = null,
        /* only for link_type 'model' */
        bool $useModelTitle = false,
    ): MenuItem {
        if (is_null($parent)) {
            $parent = \SolutionForest\FilamentTree\Support\Utils::defaultParentId();
        }

        $menuItemModel = FilamentFlexibleContentBlockPages::config()->getMenuItemModel();
        $menuId = is_numeric($menu) ? $menu : $menu->getKey();
        $parentId = is_numeric($parent) ? $parent : $parent->getKey();

        return $menuItemModel::create([
            'menu_id' => $menuId,
            'link_type' => $link_type,
            'label' => $this->normalizeLabel($label),
            'is_visible' => $isVisible,
            'target' => $target,
            'icon' => $icon,
            'parent_id' => $parentId,
            'order' => $this->getNextOrder($menuId, $parentId),
            'route' => $route,
            'url' => $url,
            'use_model_title' => $useModelTitle,
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
        MenuItem|int|null $parent = null
    ): MenuItem {
        $this->validateRoute($route);

        return $this->createMenuItem(
            menu: $menu,
            label: $label,
            link_type: MenuItem::LINK_TYPE_ROUTE,
            isVisible: $isVisible,
            target: $target,
            icon: $icon,
            parent: $parent,
            route: $route,
        );
    }

    /**
     * Create a menu item that links to an external URL.
     */
    protected function createMenuItemForUrl(
        Menu|int $menu,
        array $url,
        string|array $label,
        bool $isVisible = true,
        string $target = '_blank', // External URLs default to new tab
        ?string $icon = null,
        MenuItem|int|null $parent = null
    ): MenuItem {
        return $this->createMenuItem(
            menu: $menu,
            label: $label,
            link_type: MenuItem::LINK_TYPE_URL,
            isVisible: $isVisible,
            target: $target,
            icon: $icon,
            parent: $parent,
            url: $url,
        );
    }

    /**
     * Create a menu item that links to a Page model
     */
    protected function createMenuItemForPageCode(
        Menu|int $menu,
        string $pageCode,
        string|array|null $label = null,
        bool $useModelTitle = true,
        bool $isVisible = true,
        string $target = '_self',
        ?string $icon = null,
        MenuItem|int|null $parent = null
    ): MenuItem {
        $model = $this->getPageModel()::code($pageCode)->first();

        $menuItem = $this->createMenuItemForModel(
            menu: $menu,
            model: $model,
            label: $label,
            link_type: FilamentFlexibleContentBlockPages::config()->getPageModel()->getMorphClass(),
            useModelTitle: $useModelTitle,
            isVisible: $isVisible,
            target: $target,
            icon: $icon,
            parent: $parent,
        );

        return $menuItem;
    }

    /**
     * Create a menu item that links to a model
     */
    protected function createMenuItemForModel(
        Menu|int $menu,
        Model $model,
        string|array|null $label = null,
        string $link_type = 'model',
        bool $useModelTitle = true,
        bool $isVisible = true,
        string $target = '_self',
        ?string $icon = null,
        MenuItem|int|null $parent = null
    ): MenuItem {
        // If no label provided and useModelTitle is true, try to get model menu label
        if ($label === null && $useModelTitle && $model instanceof HasMenuLabel) {
            $label = $model->getMenuLabel();
        }

        $menuItem = $this->createMenuItem(
            menu: $menu,
            label: $label,
            link_type: $link_type,
            isVisible: $isVisible,
            target: $target,
            icon: $icon,
            parent: $parent,
            useModelTitle: $label ? false : $useModelTitle,
        );

        // Set the polymorphic relationship
        $menuItem->linkable()->associate($model);
        $menuItem->save();

        return $menuItem;
    }

    protected function getMenuModel()
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuModel();
    }

    protected function getPageModel()
    {
        return FilamentFlexibleContentBlockPages::config()->getPageModel();
    }

    protected function doesMenuExist(string $code): bool
    {
        return $this->getMenuModel()::code($code)->count() > 0;
    }

    /**
     * Convert string labels to translatable array format.
     */
    protected function normalizeLabel(string|array $label): array
    {
        if (is_string($label)) {
            return [app()->getLocale() => $label];
        }

        return $label;
    }

    /**
     * Get the next order value for items within the same menu/parent scope.
     */
    protected function getNextOrder(int $menuId, int $parentId): int
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
    protected function validateRoute(string $route): void
    {
        if (! Route::has($route)) {
            throw new InvalidArgumentException("Route '{$route}' does not exist.");
        }
    }
}
