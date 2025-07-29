<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Components;

use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class MenuTreeBuilder extends Field
{
    use HasExtraAlpineAttributes;

    protected string $view = 'filament-flexible-content-block-pages::filament.form.components.menu-tree-builder';

    protected int $maxDepth = 2;

    protected ?int $menuId = null;

    public function maxDepth(int $maxDepth): static
    {
        $this->maxDepth = $maxDepth;

        return $this;
    }

    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    public function menuId(?int $menuId): static
    {
        $this->menuId = $menuId;

        return $this;
    }

    public function getMenuId(): ?int
    {
        return $this->menuId;
    }

    public function getMenuItems(): array
    {
        if (! $this->menuId) {
            return [];
        }

        $menu = FilamentFlexibleContentBlockPages::config()->getMenuModel()::find($this->menuId);

        if (! $menu) {
            return [];
        }

        $items = $menu->allMenuItems()
            ->with('linkable')
            ->get();

        // Convert to tree structure manually since Kalnoy's toTree() might need special handling
        return $this->buildTree($items->toArray());
    }

    protected function buildTree(array $items, $parentId = null): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($items, $item['id']);
                if (! empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }
}
